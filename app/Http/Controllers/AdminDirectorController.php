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
        $validated = $request->validate([
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
            'works_raw' => ['required', 'string', function ($attribute, $value, $fail) {
                $decoded = json_decode($value, true);
                if (!is_array($decoded)) {
                    $fail(__('The works field must be a valid JSON array.'));
                }
            }],
        ]);

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
        $validated = $request->validate([
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
            'works_raw' => ['required', 'string', function ($attribute, $value, $fail) {
                $decoded = json_decode($value, true);
                if (!is_array($decoded)) {
                    $fail(__('The works field must be a valid JSON array.'));
                }
            }],
        ]);

        $data = $this->mapInputs($request, $validated);

        if ($request->hasFile('bio_image_file')) {
            $file = $request->file('bio_image_file');
            $filename = $validated['slug'] . '_' . time() . '.jpg';
            $destPath = public_path('images/directors/' . $filename);
            
            if ($this->resizeAndCropImage($file, $destPath)) {
                $oldImage = $director->bio_image;
                if (!empty($oldImage) && str_starts_with($oldImage, '/images/directors/')) {
                    $oldPath = public_path($oldImage);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $data['bio_image'] = '/images/directors/' . $filename;
            }
        } elseif (empty($data['bio_image'])) {
            $oldImage = $director->bio_image;
            if (!empty($oldImage) && str_starts_with($oldImage, '/images/directors/')) {
                $oldPath = public_path($oldImage);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
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

        $director->delete();

        return response()->json([
            'message' => __('Director deleted.'),
        ]);
    }

    private function mapInputs(Request $request, array $validated): array
    {
        // Parse bio paragraph lines
        $bioParagraphs = array_values(array_filter(
            array_map('trim', explode("\n", str_replace("\r", "", $validated['bio_raw']))),
            fn ($line) => $line !== ''
        ));

        // Map stats
        $stats = [
            [
                'value' => $validated['stat_1_value'],
                'suffix' => $validated['stat_1_suffix'] ?? '',
                'label' => $validated['stat_1_label'],
            ],
            [
                'value' => $validated['stat_2_value'],
                'suffix' => $validated['stat_2_suffix'] ?? '',
                'label' => $validated['stat_2_label'],
            ],
            [
                'value' => $validated['stat_3_value'],
                'suffix' => $validated['stat_3_suffix'] ?? '',
                'label' => $validated['stat_3_label'],
            ],
        ];

        // Parse works JSON and auto-extract thumbnails
        $works = json_decode($validated['works_raw'], true);
        foreach ($works as &$work) {
            if (empty($work['image']) && !empty($work['video_url'])) {
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

    private function getYoutubeThumbnail(string $url): string
    {
        $regExp = '/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/';
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

        $targetWidth = 1920;
        $targetHeight = 1080;

        $origRatio = $origWidth / $origHeight;
        $targetRatio = $targetWidth / $targetHeight;

        $srcX = 0;
        $srcY = 0;
        $srcWidth = $origWidth;
        $srcHeight = $origHeight;

        if ($origRatio > $targetRatio) {
            $srcWidth = (int) ($origHeight * $targetRatio);
            $srcX = (int) (($origWidth - $srcWidth) / 2);
        } else {
            $srcHeight = (int) ($origWidth / $targetRatio);
            $srcY = (int) (($origHeight - $srcHeight) / 2);
        }

        $destination = imagecreatetruecolor($targetWidth, $targetHeight);

        imagecopyresampled(
            $destination,
            $source,
            0, 0,
            $srcX, $srcY,
            $targetWidth, $targetHeight,
            $srcWidth, $srcHeight
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
