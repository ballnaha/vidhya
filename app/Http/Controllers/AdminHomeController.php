<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
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
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'hero_video_file' => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime', 'max:102400'],
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

        return redirect()
            ->route('admin.home')
            ->with('status', __('Home video updated.'));
    }
}