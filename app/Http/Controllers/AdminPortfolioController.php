<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminPortfolioController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'portfolios' => $this->portfolios($request->string('search')->toString()),
            'services' => \App\Models\Service::query()->orderBy('sort_order')->get(),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'video_url' => ['nullable', 'string', 'max:255'],
            'video_aspect_ratio' => ['nullable', Rule::in(['16:9', '9:16'])],
            'span' => ['required', 'string', 'max:50'],
            'show_in_portfolio' => ['required', 'boolean'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'sort_order' => ['required', 'integer', 'min:0', Rule::unique('portfolios', 'sort_order')],
        ]);

        $data = [
            'title' => $validated['title'],
            'service_id' => $validated['service_id'] ?? null,
            'video_url' => $validated['video_url'],
            'video_aspect_ratio' => $validated['video_aspect_ratio'] ?? '16:9',
            'span' => $validated['span'],
            'show_in_portfolio' => $validated['show_in_portfolio'],
            'sort_order' => $validated['sort_order'],
        ];

        if ($request->hasFile('image_file')) {
            $data['image'] = $this->storePortfolioImage($request->file('image_file'));
        } elseif (!empty($validated['image'])) {
            $data['image'] = $validated['image'];
        } elseif (!empty($validated['video_url'])) {
            $data['image'] = $this->getYoutubeThumbnail($validated['video_url']);
        } else {
            $data['image'] = '/images/ai-director.jpg'; // fallback
        }

        $portfolio = DB::transaction(function () use ($data) {
            $portfolio = Portfolio::create($data);
            $this->normalizePortfolioOrder();

            return $portfolio->refresh();
        });

        return response()->json([
            'message' => __('Portfolio work created.'),
            'portfolio' => $this->serializePortfolio($portfolio),
        ], 201);
    }

    public function update(Request $request, Portfolio $portfolio): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'video_url' => ['nullable', 'string', 'max:255'],
            'video_aspect_ratio' => ['nullable', Rule::in(['16:9', '9:16'])],
            'span' => ['required', 'string', 'max:50'],
            'show_in_portfolio' => ['required', 'boolean'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'sort_order' => ['required', 'integer', 'min:0', Rule::unique('portfolios', 'sort_order')->ignore($portfolio)],
        ]);

        $data = [
            'title' => $validated['title'],
            'service_id' => $validated['service_id'] ?? null,
            'video_url' => $validated['video_url'],
            'video_aspect_ratio' => $validated['video_aspect_ratio'] ?? $portfolio->video_aspect_ratio ?? '16:9',
            'span' => $validated['span'],
            'show_in_portfolio' => $validated['show_in_portfolio'],
            'sort_order' => $validated['sort_order'],
        ];

        if ($request->hasFile('image_file')) {
            $data['image'] = $this->storePortfolioImage($request->file('image_file'));

            // Delete the previous upload only after the replacement is ready.
            if ($portfolio->image && file_exists(public_path($portfolio->image)) && str_starts_with($portfolio->image, '/images/portfolios/')) {
                @unlink(public_path($portfolio->image));
            }
        } else {
            $existingImage = $request->input('image') ?: $portfolio->image;

            // If the current image is a YouTube auto-thumbnail, regenerate it from
            // the (possibly changed) video_url so swapping the URL updates the cover.
            if ($this->isYoutubeThumbnail($existingImage) || empty($existingImage)) {
                if (!empty($validated['video_url'])) {
                    $data['image'] = $this->getYoutubeThumbnail($validated['video_url']);
                } else {
                    $data['image'] = $existingImage ?: '/images/ai-director.jpg';
                }
            } else {
                $data['image'] = $existingImage;
            }
        }

        $portfolio = DB::transaction(function () use ($portfolio, $data) {
            $portfolio->update($data);
            $this->normalizePortfolioOrder();

            return $portfolio->refresh();
        });

        return response()->json([
            'message' => __('Portfolio work updated.'),
            'portfolio' => $this->serializePortfolio($portfolio),
        ]);
    }

    public function destroy(Portfolio $portfolio): JsonResponse
    {
        // Delete image file if custom
        if ($portfolio->image && file_exists(public_path($portfolio->image)) && str_starts_with($portfolio->image, '/images/portfolios/')) {
            @unlink(public_path($portfolio->image));
        }

        DB::transaction(function () use ($portfolio) {
            $portfolio->delete();
            $this->normalizePortfolioOrder();
        });

        return response()->json([
            'message' => __('Portfolio work deleted.'),
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:portfolios,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $sortSlots = Portfolio::query()
                ->whereIn('id', $validated['ids'])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('sort_order')
                ->all();

            foreach ($validated['ids'] as $id) {
                Portfolio::whereKey($id)->update([
                    'sort_order' => -$id,
                ]);
            }

            foreach ($validated['ids'] as $index => $id) {
                Portfolio::whereKey($id)->update([
                    'sort_order' => $sortSlots[$index],
                ]);
            }
        });

        return response()->json([
            'message' => __('Portfolio works reordered successfully.'),
        ]);
    }

    private function portfolios(string $search = ''): array
    {
        return Portfolio::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('video_url', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Portfolio $portfolio) => $this->serializePortfolio($portfolio))
            ->all();
    }

    private function normalizePortfolioOrder(): void
    {
        $ids = Portfolio::query()->orderBy('sort_order')->orderBy('id')->pluck('id')->all();

        foreach ($ids as $id) {
            Portfolio::whereKey($id)->update(['sort_order' => -$id]);
        }

        foreach ($ids as $index => $id) {
            Portfolio::whereKey($id)->update(['sort_order' => ($index + 1) * 10]);
        }
    }

    private function serializePortfolio(Portfolio $portfolio): array
    {
        return [
            'id' => $portfolio->id,
            'title' => $portfolio->title,
            'service_id' => $portfolio->service_id,
            'service_title' => $portfolio->service?->title ?? '',
            'video_url' => $portfolio->video_url ?? '',
            'video_aspect_ratio' => $portfolio->video_aspect_ratio ?? '16:9',
            'image' => $portfolio->image,
            'span' => $portfolio->span,
            'show_in_portfolio' => $portfolio->show_in_portfolio,
            'sort_order' => $portfolio->sort_order,
            'created_at' => $portfolio->created_at?->format('M j, Y'),
        ];
    }

    private function isYoutubeThumbnail(string $url): bool
    {
        return str_contains($url, 'img.youtube.com') || str_contains($url, 'ytimg.com');
    }

    private function getYoutubeThumbnail(string $url): string
    {
        $regExp = '/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|shorts\/|watch\?v=|\&v=)([^#\&\?]*).*/';
        if (preg_match($regExp, $url, $matches)) {
            if (isset($matches[2]) && strlen($matches[2]) === 11) {
                return "https://img.youtube.com/vi/{$matches[2]}/mqdefault.jpg";
            }
        }
        return '/images/ai-director.jpg'; // fallback
    }

    private function storePortfolioImage(UploadedFile $file): string
    {
        $directory = public_path('images/portfolios');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $baseName = Str::slug($originalName) ?: 'portfolio';
        $filename = 'work_'.time().'_'.Str::lower(Str::random(8)).'_'.$baseName.'.webp';
        $destinationPath = $directory.DIRECTORY_SEPARATOR.$filename;

        if (! $this->resizeAndCropImage($file, $destinationPath)) {
            if (file_exists($destinationPath)) {
                @unlink($destinationPath);
            }

            throw ValidationException::withMessages([
                'image_file' => __('The image could not be resized. Please upload another JPEG, PNG, or WebP image.'),
            ]);
        }

        return '/images/portfolios/'.$filename;
    }

    private function resizeAndCropImage(UploadedFile $uploadedFile, string $destinationPath): bool
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

        $maxWidth = 1600;
        $maxHeight = 900;
        $targetRatio = $maxWidth / $maxHeight;
        $sourceRatio = $origWidth / $origHeight;

        // Crop from the centre to the 16:9 ratio used by portfolio cards.
        if ($sourceRatio > $targetRatio) {
            $cropHeight = $origHeight;
            $cropWidth = (int) round($origHeight * $targetRatio);
            $sourceX = (int) floor(($origWidth - $cropWidth) / 2);
            $sourceY = 0;
        } else {
            $cropWidth = $origWidth;
            $cropHeight = (int) round($origWidth / $targetRatio);
            $sourceX = 0;
            $sourceY = (int) floor(($origHeight - $cropHeight) / 2);
        }

        // Never upscale small uploads.
        $scale = min(1, $maxWidth / $cropWidth, $maxHeight / $cropHeight);
        $targetWidth = max(1, (int) round($cropWidth * $scale));
        $targetHeight = max(1, (int) round($cropHeight * $scale));

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
            $sourceX, $sourceY,
            $targetWidth, $targetHeight,
            $cropWidth, $cropHeight
        );

        $dir = dirname($destinationPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // Always encode portfolio covers as WebP to reduce the actual file size,
        // not only their pixel dimensions.
        $success = imagewebp($destination, $destinationPath, 76);

        imagedestroy($source);
        imagedestroy($destination);

        return $success;
    }
}
