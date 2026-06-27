<?php

namespace App\Http\Controllers;

use App\Models\Director;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminDirectorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'directors' => $this->directors($request->string('search')->toString()),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('directors', 'slug')],
            'eyebrow' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'bio_title_white' => ['required', 'string', 'max:255'],
            'bio_title_gradient' => ['required', 'string', 'max:255'],
            'bio_image' => ['nullable', 'string', 'max:255'],
            'bio_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'bio_alt' => ['required', 'string', 'max:255'],
            'works_eyebrow' => ['required', 'string', 'max:255'],
            'works_title_white' => ['required', 'string', 'max:255'],
            'works_title_muted' => ['required', 'string', 'max:255'],
            'bio_raw' => ['required', 'string'],
            'stat_1_value' => ['required', 'string', 'max:50'],
            'stat_1_suffix' => ['nullable', 'string', 'max:20'],
            'stat_1_label' => ['required', 'string', 'max:255'],
            'stat_2_value' => ['required', 'string', 'max:50'],
            'stat_2_suffix' => ['nullable', 'string', 'max:20'],
            'stat_2_label' => ['required', 'string', 'max:255'],
            'stat_3_value' => ['required', 'string', 'max:50'],
            'stat_3_suffix' => ['nullable', 'string', 'max:20'],
            'stat_3_label' => ['required', 'string', 'max:255'],
            'works_raw' => ['required', 'string', function ($attribute, $value, $fail) use ($request) {
                $decoded = json_decode($value, true);
                if (!is_array($decoded)) {
                    $fail(__('The works field must be a valid JSON array.'));
                    return;
                }
                foreach ($decoded as $index => $work) {
                    $hasFile = $request->hasFile('work_image_file_' . $index);
                    if (empty($work['title'])) {
                        $fail(__('Each work must have a title.'));
                        return;
                    }
                    if (empty($work['video_url']) && empty($work['image']) && !$hasFile) {
                        $fail(__('Each work must have a video URL or an uploaded image.'));
                        return;
                    }
                }
            }],
        ];

        $worksDecoded = json_decode($request->input('works_raw'), true);
        if (is_array($worksDecoded)) {
            foreach ($worksDecoded as $index => $work) {
                $rules['work_image_file_' . $index] = ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'];
            }
        }

        $validated = $request->validate($rules);

        $data = $this->mapInputs($request, $validated);

        if ($request->hasFile('bio_image_file')) {
            $file = $request->file('bio_image_file');
            $filename = $validated['slug'] . '_' . time() . '.jpg';
            $destPath = public_path('images/directors/' . $filename);
            
            if ($this->resizeAndCropImage($file, $destPath)) {
                $data['bio_image'] = '/images/directors/' . $filename;
            }
        }

        $director = Director::create($data);

        return response()->json([
            'message' => __('Director created.'),
            'director' => $this->serializeDirector($director),
        ], 201);
    }

    public function checkSlug(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'alpha_dash'],
            'ignore' => ['nullable', 'integer', 'exists:directors,id'],
        ]);

        $exists = Director::query()
            ->where('slug', $validated['slug'])
            ->when($validated['ignore'] ?? null, fn ($query, $id) => $query->whereKeyNot($id))
            ->exists();

        return response()->json([
            'exists' => $exists,
        ]);
    }

    public function update(Request $request, Director $director): JsonResponse
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('directors', 'slug')->ignore($director->id)],
            'eyebrow' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'bio_title_white' => ['required', 'string', 'max:255'],
            'bio_title_gradient' => ['required', 'string', 'max:255'],
            'bio_image' => ['nullable', 'string', 'max:255'],
            'bio_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'bio_alt' => ['required', 'string', 'max:255'],
            'works_eyebrow' => ['required', 'string', 'max:255'],
            'works_title_white' => ['required', 'string', 'max:255'],
            'works_title_muted' => ['required', 'string', 'max:255'],
            'bio_raw' => ['required', 'string'],
            'stat_1_value' => ['required', 'string', 'max:50'],
            'stat_1_suffix' => ['nullable', 'string', 'max:20'],
            'stat_1_label' => ['required', 'string', 'max:255'],
            'stat_2_value' => ['required', 'string', 'max:50'],
            'stat_2_suffix' => ['nullable', 'string', 'max:20'],
            'stat_2_label' => ['required', 'string', 'max:255'],
            'stat_3_value' => ['required', 'string', 'max:50'],
            'stat_3_suffix' => ['nullable', 'string', 'max:20'],
            'stat_3_label' => ['required', 'string', 'max:255'],
            'works_raw' => ['required', 'string', function ($attribute, $value, $fail) use ($request) {
                $decoded = json_decode($value, true);
                if (!is_array($decoded)) {
                    $fail(__('The works field must be a valid JSON array.'));
                    return;
                }
                foreach ($decoded as $index => $work) {
                    $hasFile = $request->hasFile('work_image_file_' . $index);
                    if (empty($work['title'])) {
                        $fail(__('Each work must have a title.'));
                        return;
                    }
                    if (empty($work['video_url']) && empty($work['image']) && !$hasFile) {
                        $fail(__('Each work must have a video URL or an uploaded image.'));
                        return;
                    }
                }
            }],
        ];

        $worksDecoded = json_decode($request->input('works_raw'), true);
        if (is_array($worksDecoded)) {
            foreach ($worksDecoded as $index => $work) {
                $rules['work_image_file_' . $index] = ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'];
            }
        }

        $validated = $request->validate($rules);

        $oldImages = [];
        if ($director->works && is_array($director->works)) {
            foreach ($director->works as $oldWork) {
                if (!empty($oldWork['image']) && str_starts_with($oldWork['image'], '/images/directors/')) {
                    $oldImages[] = $oldWork['image'];
                }
            }
        }

        $data = $this->mapInputs($request, $validated);

        if ($request->hasFile('bio_image_file')) {
            $file = $request->file('bio_image_file');
            $filename = $validated['slug'] . '_' . time() . '.jpg';
            $destPath = public_path('images/directors/' . $filename);
            
            if ($this->resizeAndCropImage($file, $destPath)) {
                $oldBioImage = $director->bio_image;
                if (!empty($oldBioImage) && str_starts_with($oldBioImage, '/images/directors/')) {
                    $oldPath = public_path($oldBioImage);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $data['bio_image'] = '/images/directors/' . $filename;
            }
        } elseif (empty($data['bio_image'])) {
            $oldBioImage = $director->bio_image;
            if (!empty($oldBioImage) && str_starts_with($oldBioImage, '/images/directors/')) {
                $oldPath = public_path($oldBioImage);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        // After mapping inputs, find new custom work images
        $newImages = [];
        if (isset($data['works']) && is_array($data['works'])) {
            foreach ($data['works'] as $newWork) {
                if (!empty($newWork['image']) && str_starts_with($newWork['image'], '/images/directors/')) {
                    $newImages[] = $newWork['image'];
                }
            }
        }

        // Delete custom work files that were in old works but are no longer in new works
        $deletedImages = array_diff($oldImages, $newImages);
        foreach ($deletedImages as $deletedImage) {
            $filePath = public_path($deletedImage);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $director->update($data);

        return response()->json([
            'message' => __('Director updated.'),
            'director' => $this->serializeDirector($director),
        ]);
    }

    public function destroy(Director $director): JsonResponse
    {
        $oldImage = $director->bio_image;
        if (!empty($oldImage) && str_starts_with($oldImage, '/images/directors/')) {
            $oldPath = public_path($oldImage);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        // Delete work custom images
        if ($director->works && is_array($director->works)) {
            foreach ($director->works as $work) {
                if (!empty($work['image']) && str_starts_with($work['image'], '/images/directors/')) {
                    $filePath = public_path($work['image']);
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
            }
        }

        $director->delete();

        return response()->json([
            'message' => __('Director deleted.'),
        ]);
    }

    private function mapInputs(Request $request, array $validated): array
    {
        // Parse bio paragraph lines
        $bioRaw = $validated['bio_raw'] ?? '';
        $bioParagraphs = array_values(array_filter(
            array_map('trim', explode("\n", str_replace("\r", "", $bioRaw))),
            fn ($line) => $line !== ''
        ));

        // Map stats
        $stats = [
            [
                'value' => $validated['stat_1_value'] ?? '0',
                'suffix' => $validated['stat_1_suffix'] ?? '',
                'label' => $validated['stat_1_label'] ?? 'Stat',
            ],
            [
                'value' => $validated['stat_2_value'] ?? '0',
                'suffix' => $validated['stat_2_suffix'] ?? '',
                'label' => $validated['stat_2_label'] ?? 'Stat',
            ],
            [
                'value' => $validated['stat_3_value'] ?? '0',
                'suffix' => $validated['stat_3_suffix'] ?? '',
                'label' => $validated['stat_3_label'] ?? 'Stat',
            ],
        ];

        // Parse works JSON, handle file uploads, and auto-extract thumbnails
        $works = json_decode($validated['works_raw'], true);
        foreach ($works as $index => &$work) {
            $fileKey = 'work_image_file_' . $index;
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $filename = $validated['slug'] . '_work_' . $index . '_' . time() . '.jpg';
                $destPath = public_path('images/directors/' . $filename);
                
                // Ensure directors image folder exists
                $dir = public_path('images/directors');
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }

                if ($this->resizeAndCropImage($file, $destPath)) {
                    $work['image'] = '/images/directors/' . $filename;
                } else {
                    $fallbackFilename = $validated['slug'] . '_work_' . $index . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->move($dir, $fallbackFilename);
                    $work['image'] = '/images/directors/' . $fallbackFilename;
                }
            } elseif (!empty($work['video_url']) && (empty($work['image']) || $this->isYoutubeThumbnail($work['image']))) {
                $work['image'] = $this->getYoutubeThumbnail($work['video_url']);
            }
        }

        return [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'slug' => $validated['slug'],
            'eyebrow' => $validated['eyebrow'],
            'role' => $validated['role'],
            'bio_title_white' => $validated['bio_title_white'],
            'bio_title_gradient' => $validated['bio_title_gradient'],
            'bio_image' => $validated['bio_image'] ?? '',
            'bio_alt' => $validated['bio_alt'],
            'works_eyebrow' => $validated['works_eyebrow'],
            'works_title_white' => $validated['works_title_white'],
            'works_title_muted' => $validated['works_title_muted'],
            'bio' => $bioParagraphs,
            'stats' => $stats,
            'works' => $works,
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

    private function resizeAndCropImage($uploadedFile, $destinationPath): bool
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

        $maxWidth = 1920;
        $maxHeight = 1080;

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

        $success = imagejpeg($destination, $destinationPath, 90);

        imagedestroy($source);
        imagedestroy($destination);

        return $success;
    }

    private function directors(string $search = ''): array
    {
        return Director::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (Director $director) => $this->serializeDirector($director))
            ->all();
    }

    private function serializeDirector(Director $director): array
    {
        $bioRaw = implode("\n\n", $director->bio ?? []);
        $worksRaw = json_encode($director->works ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return [
            'id' => $director->id,
            'first_name' => $director->first_name,
            'last_name' => $director->last_name,
            'slug' => $director->slug,
            'eyebrow' => $director->eyebrow,
            'role' => $director->role,
            'bio_title_white' => $director->bio_title_white,
            'bio_title_gradient' => $director->bio_title_gradient,
            'bio_image' => $director->bio_image,
            'bio_alt' => $director->bio_alt,
            'works_eyebrow' => $director->works_eyebrow,
            'works_title_white' => $director->works_title_white,
            'works_title_muted' => $director->works_title_muted,
            'bio_raw' => $bioRaw,
            'works_raw' => $worksRaw,
            'stat_1_value' => $director->stats[0]['value'] ?? '',
            'stat_1_suffix' => $director->stats[0]['suffix'] ?? '',
            'stat_1_label' => $director->stats[0]['label'] ?? '',
            'stat_2_value' => $director->stats[1]['value'] ?? '',
            'stat_2_suffix' => $director->stats[1]['suffix'] ?? '',
            'stat_2_label' => $director->stats[1]['label'] ?? '',
            'stat_3_value' => $director->stats[2]['value'] ?? '',
            'stat_3_suffix' => $director->stats[2]['suffix'] ?? '',
            'stat_3_label' => $director->stats[2]['label'] ?? '',
            'created_at' => $director->created_at?->format('M j, Y'),
        ];
    }
}
