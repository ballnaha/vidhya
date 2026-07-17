<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('pages.admin.clients', [
            'clients' => $clients,
            'carouselSpeed' => SiteSetting::clientCarouselSpeed(),
        ]);
    }

    public function data(): JsonResponse
    {
        $clients = Client::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Client $client) => $this->serializeClient($client))
            ->values();

        return response()->json([
            'clients' => $clients,
            'carousel_speed' => SiteSetting::clientCarouselSpeed(),
        ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function updateCarouselSpeed(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'carousel_speed' => ['required', 'integer', 'min:10', 'max:300'],
        ]);

        SiteSetting::setValue(SiteSetting::CLIENT_CAROUSEL_SPEED, (string) $validated['carousel_speed']);

        return response()->json([
            'message' => __('Client logo speed updated successfully.'),
            'carousel_speed' => SiteSetting::clientCarouselSpeed(),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo_file' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'website_url' => ['nullable', 'url', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $client = Client::create([
            'name' => $validated['name'],
            'logo' => $this->storeLogo($request->file('logo_file')),
            'website_url' => $validated['website_url'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $validated['sort_order'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => __('Client created successfully.'), 'client' => $this->serializeClient($client)], 201);
        }

        return back()->with('status', __('Client created successfully.'));
    }

    public function update(Request $request, Client $client): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'website_url' => ['nullable', 'url', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $data = [
            'name' => $validated['name'],
            'website_url' => $validated['website_url'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $validated['sort_order'],
        ];

        if ($request->hasFile('logo_file')) {
            $data['logo'] = $this->storeLogo($request->file('logo_file'));
            $this->deleteLogo($client->logo);
        }

        $client->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => __('Client updated successfully.'), 'client' => $this->serializeClient($client->refresh())]);
        }

        return back()->with('status', __('Client updated successfully.'));
    }

    public function destroy(Request $request, Client $client): RedirectResponse|JsonResponse
    {
        $this->deleteLogo($client->logo);
        $client->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => __('Client deleted successfully.')]);
        }

        return back()->with('status', __('Client deleted successfully.'));
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:clients,id'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['ids'] as $index => $id) {
                Client::whereKey($id)->update(['sort_order' => ($index + 1) * 10]);
            }
        });

        return response()->json(['message' => __('Clients reordered successfully.')]);
    }

    private function storeLogo(UploadedFile $file): string
    {
        $directory = public_path('images/client');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'client-logo';
        $filename = $baseName.'-'.time().'-'.Str::lower(Str::random(6)).'.'.$file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return '/images/client/'.$filename;
    }

    private function deleteLogo(?string $logo): void
    {
        if ($logo && $logo !== '/images/client/BOSS.png' && str_starts_with($logo, '/images/client/') && file_exists(public_path($logo))) {
            @unlink(public_path($logo));
        }
    }

    private function serializeClient(Client $client): array
    {
        return [
            'id' => $client->id,
            'name' => $client->name,
            'logo' => $client->logo,
            'website_url' => $client->website_url ?? '',
            'is_active' => (bool) $client->is_active,
            'sort_order' => $client->sort_order,
            'created_at' => $client->created_at?->format('M j, Y'),
        ];
    }
}
