<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'services' => $this->services($request->string('search')->toString()),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'num' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'bullets_raw' => ['required', 'string'],
            'accent' => ['required', 'string', 'max:50'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'sort_order' => ['required', 'integer', 'min:0', Rule::unique('services', 'sort_order')],
        ]);

        $bullets = array_filter(
            array_map('trim', explode("\n", $validated['bullets_raw'])),
            fn ($bullet) => $bullet !== ''
        );

        $data = [
            'num' => $validated['num'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'bullets' => array_values($bullets),
            'accent' => $validated['accent'],
            'sort_order' => $validated['sort_order'],
        ];

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . strtolower(str_replace(' ', '_', $file->getClientOriginalName()));
            $destPath = public_path('images/services/' . $filename);
            
            if ($this->resizeImage($file, $destPath)) {
                $data['image'] = '/images/services/' . $filename;
            }
        } else {
            $data['image'] = null;
        }

        $service = DB::transaction(function () use ($data) {
            $service = Service::create($data);
            $this->normalizeServiceOrder();

            return $service->refresh();
        });

        return response()->json([
            'message' => __('Service created.'),
            'service' => $this->serializeService($service),
        ], 201);
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        $validated = $request->validate([
            'num' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'bullets_raw' => ['required', 'string'],
            'accent' => ['required', 'string', 'max:50'],
            'image' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'sort_order' => ['required', 'integer', 'min:0', Rule::unique('services', 'sort_order')->ignore($service)],
        ]);

        $bullets = array_filter(
            array_map('trim', explode("\n", $validated['bullets_raw'])),
            fn ($bullet) => $bullet !== ''
        );

        $data = [
            'num' => $validated['num'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'bullets' => array_values($bullets),
            'accent' => $validated['accent'],
            'sort_order' => $validated['sort_order'],
        ];

        if ($request->hasFile('image_file')) {
            // Delete old file if exists and is not the default fallback images
            if ($service->image && file_exists(public_path($service->image)) && strpos($service->image, '/images/services/') === 0 && strpos($service->image, 'previs.webp') === false) {
                @unlink(public_path($service->image));
            }

            $file = $request->file('image_file');
            $filename = time() . '_' . strtolower(str_replace(' ', '_', $file->getClientOriginalName()));
            $destPath = public_path('images/services/' . $filename);
            
            if ($this->resizeImage($file, $destPath)) {
                $data['image'] = '/images/services/' . $filename;
            }
        } else {
            $data['image'] = $request->filled('image') ? $request->string('image')->toString() : null;
        }

        $service = DB::transaction(function () use ($service, $data) {
            $service->update($data);
            $this->normalizeServiceOrder();

            return $service->refresh();
        });

        return response()->json([
            'message' => __('Service updated.'),
            'service' => $this->serializeService($service),
        ]);
    }

    public function destroy(Service $service): JsonResponse
    {
        // Delete image file if exists
        if ($service->image && file_exists(public_path($service->image)) && strpos($service->image, '/images/services/') === 0 && strpos($service->image, 'previs.webp') === false) {
            @unlink(public_path($service->image));
        }

        DB::transaction(function () use ($service) {
            $service->delete();
            $this->normalizeServiceOrder();
        });

        return response()->json([
            'message' => __('Service deleted.'),
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:services,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $sortSlots = Service::query()
                ->whereIn('id', $validated['ids'])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('sort_order')
                ->all();

            foreach ($validated['ids'] as $id) {
                Service::whereKey($id)->update(['sort_order' => -$id]);
            }

            foreach ($validated['ids'] as $index => $id) {
                Service::whereKey($id)->update(['sort_order' => $sortSlots[$index]]);
            }
        });

        return response()->json([
            'message' => __('Services reordered successfully.'),
        ]);
    }

    private function services(string $search = ''): array
    {
        return Service::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('num', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Service $service) => $this->serializeService($service))
            ->all();
    }

    private function normalizeServiceOrder(): void
    {
        $ids = Service::query()->orderBy('sort_order')->orderBy('id')->pluck('id')->all();

        foreach ($ids as $id) {
            Service::whereKey($id)->update(['sort_order' => -$id]);
        }

        foreach ($ids as $index => $id) {
            Service::whereKey($id)->update(['sort_order' => ($index + 1) * 10]);
        }
    }

    private function serializeService(Service $service): array
    {
        $bulletsRaw = implode("\n", $service->bullets ?? []);

        return [
            'id' => $service->id,
            'num' => $service->num,
            'title' => $service->title,
            'description' => $service->description,
            'bullets_raw' => $bulletsRaw,
            'bullets' => $service->bullets ?? [],
            'accent' => $service->accent,
            'image' => $service->image,
            'sort_order' => $service->sort_order,
            'created_at' => $service->created_at?->format('M j, Y'),
        ];
    }

    private function resizeImage($uploadedFile, $destinationPath): bool
    {
        $info = getimagesize($uploadedFile->getRealPath());
        if ($info === false) {
            return false;
        }

        $mime = $info['mime'];
        $source = null;

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $source = imagecreatefromjpeg($uploadedFile->getRealPath());
                break;
            case 'image/png':
                $source = imagecreatefrompng($uploadedFile->getRealPath());
                break;
            case 'image/webp':
                $source = imagecreatefromwebp($uploadedFile->getRealPath());
                break;
            default:
                return false;
        }

        if (!$source) {
            return false;
        }

        $origWidth = imagesx($source);
        $origHeight = imagesy($source);

        $maxWidth = 1200;
        $maxHeight = 1200;

        $targetWidth = $origWidth;
        $targetHeight = $origHeight;

        $ratio = $origWidth / $origHeight;

        if ($origWidth > $maxWidth || $origHeight > $maxHeight) {
            if ($origWidth / $maxWidth > $origHeight / $maxHeight) {
                $targetWidth = $maxWidth;
                $targetHeight = (int) ($maxWidth / $ratio);
            } else {
                $targetHeight = $maxHeight;
                $targetWidth = (int) ($maxHeight * $ratio);
            }
        }

        $destination = imagecreatetruecolor($targetWidth, $targetHeight);

        // Keep alpha channel for PNG/WEBP
        if ($mime == 'image/png' || $mime == 'image/webp') {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
        }

        imagecopyresampled(
            $destination,
            $source,
            0, 0,
            0, 0,
            $targetWidth, $targetHeight,
            $origWidth, $origHeight
        );

        $dir = dirname($destinationPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($mime == 'image/png') {
            $success = imagepng($destination, $destinationPath, 8);
        } else if ($mime == 'image/webp') {
            $success = imagewebp($destination, $destinationPath, 85);
        } else {
            $success = imagejpeg($destination, $destinationPath, 85);
        }

        imagedestroy($source);
        imagedestroy($destination);

        return $success;
    }
}
