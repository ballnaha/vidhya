<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminHomeController extends Controller
{
    public function edit(): View
    {
        return view('pages.admin.home', [
            'heroYoutubeUrl' => SiteSetting::homeHeroYoutubeUrl(),
            'heroYoutubeId' => SiteSetting::homeHeroYoutubeId(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_youtube_url' => ['required', 'url', 'max:500', function (string $attribute, mixed $value, Closure $fail): void {
                if (! SiteSetting::youtubeIdFromUrl((string) $value)) {
                    $fail(__('Please enter a valid YouTube URL.'));
                }
            }],
        ]);

        SiteSetting::setValue(SiteSetting::HOME_HERO_YOUTUBE_URL, $validated['hero_youtube_url']);

        return redirect()
            ->route('admin.home')
            ->with('status', __('Home video updated.'));
    }
}