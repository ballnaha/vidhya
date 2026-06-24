<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminHomeController extends Controller
{
    public function edit(): View
    {
        return view('pages.admin.home', [
            'heroVideoPath' => SiteSetting::homeHeroVideoPath(),
            'heroPosterPath' => SiteSetting::homeHeroPosterPath(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'hero_video_file' => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime', 'max:102400'],
            'hero_poster_data' => ['nullable', 'string'],
        ]);

        $file = $request->file('hero_video_file');
        $directory = public_path('images');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $baseName = Str::slug($originalName) ?: 'hero-video';
        $extension = $file->getClientOriginalExtension() ?: 'mp4';
        $filename = 'hero_' . time() . '_' . Str::lower(Str::random(6)) . '_' . $baseName . '.' . $extension;

        $file->move($directory, $filename);

        $videoPath = '/images/' . $filename;
        SiteSetting::setValue(SiteSetting::HOME_HERO_VIDEO_PATH, $videoPath);

        // Save poster image from client-side canvas capture (base64 data URL)
        $posterData = $request->input('hero_poster_data');
        if ($posterData && str_starts_with($posterData, 'data:image/')) {
            $posterPath = $this->savePosterFromBase64($posterData, $baseName, $directory);
            if ($posterPath) {
                SiteSetting::setValue(SiteSetting::HOME_HERO_POSTER_PATH, $posterPath);
            }
        }

        return redirect()
            ->route('admin.home')
            ->with('status', __('Home video updated.'));
    }

    public function updatePoster(Request $request): JsonResponse
    {
        $request->validate([
            'poster_data' => ['required', 'string'],
        ]);

        $posterData = $request->input('poster_data');
        if (! str_starts_with($posterData, 'data:image/')) {
            return response()->json(['error' => 'Invalid image data.'], 422);
        }

        $directory = public_path('images');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $posterPath = $this->savePosterFromBase64($posterData, 'hero', $directory);
        if (! $posterPath) {
            return response()->json(['error' => 'Could not save poster.'], 500);
        }

        SiteSetting::setValue(SiteSetting::HOME_HERO_POSTER_PATH, $posterPath);

        return response()->json([
            'success' => true,
            'poster_path' => $posterPath,
        ]);
    }

    private function savePosterFromBase64(string $dataUrl, string $baseName, string $directory): ?string
    {
        // Parse data URL: data:image/webp;base64,xxxxx...
        if (! preg_match('/^data:image\/(webp|png|jpeg);base64,(.+)$/', $dataUrl, $matches)) {
            return null;
        }

        $imageData = base64_decode($matches[2], true);
        if ($imageData === false) {
            return null;
        }

        $posterFilename = 'hero_poster_' . time() . '_' . Str::lower(Str::random(6)) . '_' . $baseName . '.webp';
        $posterFullPath = $directory . DIRECTORY_SEPARATOR . $posterFilename;

        file_put_contents($posterFullPath, $imageData);

        return '/images/' . $posterFilename;
    }
}