@php
    use App\Models\SiteSetting;
@endphp

<x-layouts::app :title="__('Home')">
    <div class="min-h-full bg-[#0a0a0c] text-white">
        <div class="space-y-6">
            <section class="border border-[#366bc3]/18 bg-[#0f0f18] p-5 lg:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.26em] text-white/35">{{ __('Admin') }}</p>
                        <h1 class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-[clamp(2.25rem,5vw,3.5rem)] font-black uppercase leading-none tracking-[-0.03em] text-transparent">{{ __('Home') }}</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-white/45">{{ __('Manage the YouTube video used as the homepage hero background.') }}</p>
                    </div>

                    <a href="{{ route('home') }}" class="inline-flex rounded border border-white/14 px-7 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white/72 transition hover:border-white/35 hover:text-white" wire:navigate.hover>
                        {{ __('View Home') }}
                    </a>
                </div>
            </section>

            @if (session('status'))
                <div
                    class="hidden"
                    data-toast-on-load
                    data-toast-variant="success"
                    data-toast-heading="{{ __('Success') }}"
                    data-toast-text="{{ session('status') }}"
                ></div>
            @endif

            <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(320px,520px)]">
                <form method="POST" action="{{ route('admin.home.update') }}" class="border border-white/8 bg-[#0d0d13] p-6" data-admin-home-form>
                    @csrf
                    @method('PATCH')

                    <div class="mb-6 border-b border-white/8 pb-4">
                        <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Hero Video') }}</h2>
                        <p class="mt-2 text-xs leading-6 text-white/38">{{ __('Paste a YouTube URL. The site will autoplay it muted, loop it, and keep the current image as a fallback while the video loads.') }}</p>
                    </div>

                    <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider" for="hero_youtube_url">{{ __('YouTube URL') }}</label>
                    <input
                        id="hero_youtube_url"
                        name="hero_youtube_url"
                        value="{{ old('hero_youtube_url', $heroYoutubeUrl) }}"
                        placeholder="https://youtu.be/lboLBQ2QaeE"
                        class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]"
                    >
                    @error('hero_youtube_url')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror

                    <div class="mt-6 flex flex-col gap-3 border-t border-white/8 pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-[11px] text-white/32">{{ __('Current video ID') }}: <span class="font-mono text-white/58">{{ $heroYoutubeId }}</span></p>
                        <button type="submit" class="inline-flex items-center justify-center gap-3 rounded px-8 py-3.5 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-home-save>
                            <svg class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true" data-admin-home-spinner>
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span data-admin-home-save-label>{{ __('Save Home Video') }}</span>
                        </button>
                    </div>
                </form>

                <article class="border border-white/8 bg-[#0d0d13] p-6">
                    <div class="mb-4 flex items-center justify-between gap-4 border-b border-white/8 pb-4">
                        <div>
                            <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Preview') }}</h2>
                            <p class="mt-1 text-xs text-white/35">{{ __('Muted background embed') }}</p>
                        </div>
                    </div>

                    <div class="relative aspect-video overflow-hidden border border-white/10 bg-black">
                        <iframe
                            src="https://www.youtube-nocookie.com/embed/{{ $heroYoutubeId }}?autoplay=1&mute=1&controls=0&loop=1&playlist={{ $heroYoutubeId }}&playsinline=1&modestbranding=1&rel=0"
                            title="{{ __('Home hero video preview') }}"
                            class="absolute inset-0 h-full w-full border-0"
                            allow="autoplay; encrypted-media; picture-in-picture"
                        ></iframe>
                    </div>

                    <p class="mt-4 break-all text-xs leading-6 text-white/38">{{ $heroYoutubeUrl }}</p>
                </article>
            </section>
        </div>
    </div>
</x-layouts::app>