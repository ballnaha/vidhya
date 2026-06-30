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
            'heroYoutubeUrl' => SiteSetting::homeHeroYoutubeUrl(),
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

    public function updateYoutube(Request $request): RedirectResponse
    {
        $request->validate([
            'hero_youtube_url' => [
                'required',
                'url',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (! \App\Models\SiteSetting::youtubeIdFromUrl($value)) {
                        $fail(__('The :attribute must be a valid YouTube URL.'));
                    }
                },
            ],
        ]);

        SiteSetting::setValue(SiteSetting::HOME_HERO_YOUTUBE_URL, $request->input('hero_youtube_url'));

        return redirect()
            ->route('admin.home')
            ->with('status', __('YouTube Reel URL updated.'));
    }

    public function updateSocialLinks(Request $request): RedirectResponse
    {
        $socials = $request->input('socials', []);
        $files = $request->file('socials', []);
        
        $savedSocials = [];
        
        foreach ($socials as $index => $social) {
            $name = $social['name'] ?? '';
            $url = $social['url'] ?? '';
            $type = $social['type'] ?? 'svg';
            $iconSvg = $social['icon_svg'] ?? '';
            $iconPath = $social['existing_icon_path'] ?? '';
            
            // Check if there is a new file uploaded for this social link
            if ($type === 'image' && isset($files[$index]['icon_file'])) {
                $file = $files[$index]['icon_file'];
                $directory = public_path('images/social');
                if (! is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $filename = 'social_' . time() . '_' . Str::lower(Str::random(4)) . '.' . $file->getClientOriginalExtension();
                $file->move($directory, $filename);
                $iconPath = '/images/social/' . $filename;
            }
            
            if ($name && $url) {
                $savedSocials[] = [
                    'name' => $name,
                    'url' => $url,
                    'type' => $type,
                    'icon_svg' => ($type === 'svg') ? $iconSvg : '',
                    'icon_path' => ($type === 'image') ? $iconPath : '',
                ];
            }
        }
        
        SiteSetting::setValue(SiteSetting::SOCIAL_LINKS, json_encode($savedSocials));
        
        return redirect()
            ->route('admin.home')
            ->with('status', __('Social media links updated.'));
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