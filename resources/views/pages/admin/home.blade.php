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
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-white/45">{{ __('Manage the hero background video displayed on the homepage.') }}</p>
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
                <div class="space-y-6">
                    {{-- Upload Video Form --}}
                    <form method="POST" action="{{ route('admin.home.update') }}" enctype="multipart/form-data" class="border border-white/8 bg-[#0d0d13] p-6" data-admin-home-form>
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="hero_poster_data" id="hero_poster_data" value="">

                        <div class="mb-6 border-b border-white/8 pb-4">
                            <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Hero Video') }}</h2>
                            <p class="mt-2 text-xs leading-6 text-white/38">{{ __('Upload a video file (MP4, WebM, or MOV). The site will autoplay it muted and loop it as the homepage hero background. A poster image will be auto-generated from the first frame.') }}</p>
                        </div>

                        <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider" for="hero_video_file">{{ __('Video File') }}</label>
                        <div class="relative">
                            <input
                                id="hero_video_file"
                                name="hero_video_file"
                                type="file"
                                accept="video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov"
                                class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition file:mr-4 file:rounded file:border-0 file:bg-white/10 file:px-4 file:py-1.5 file:text-xs file:font-semibold file:uppercase file:tracking-wider file:text-white/70 file:transition file:cursor-pointer hover:file:bg-white/20 focus:border-[#366bc3]"
                                data-admin-home-file-input
                            >
                        </div>
                        <p class="mt-2 text-[11px] text-white/28">{{ __('Max file size: 100 MB. Recommended: MP4 format, 720p resolution for best performance.') }}</p>
                        @error('hero_video_file')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror

                        {{-- Poster preview from new video --}}
                        <div class="mt-5 hidden" id="poster-preview-wrap">
                            <p class="mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Auto-generated Poster (first frame)') }}</p>
                            <div class="relative aspect-video max-w-xs overflow-hidden rounded border border-white/10 bg-black">
                                <img id="poster-preview-img" src="" alt="Poster preview" class="absolute inset-0 h-full w-full object-cover">
                            </div>
                            <p class="mt-1.5 text-[11px] text-emerald-400/70" id="poster-status">{{ __('✓ Poster captured from first frame') }}</p>
                        </div>

                        <div class="mt-6 flex flex-col gap-3 border-t border-white/8 pt-5 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-[11px] text-white/32">{{ __('Current video') }}: <span class="font-mono text-white/58">{{ basename($heroVideoPath) }}</span></p>
                            <button type="submit" class="inline-flex items-center justify-center gap-3 rounded px-8 py-3.5 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-home-save>
                                <svg class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true" data-admin-home-spinner>
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span data-admin-home-save-label>{{ __('Upload & Save') }}</span>
                            </button>
                        </div>
                    </form>

                    {{-- Generate Poster from Current Video --}}
                    <div class="border border-white/8 bg-[#0d0d13] p-6">
                        <div class="mb-5 border-b border-white/8 pb-4">
                            <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Poster Image') }}</h2>
                            <p class="mt-2 text-xs leading-6 text-white/38">{{ __('Generate a poster image from the first frame of the current hero video. This image is shown while the video is loading.') }}</p>
                        </div>

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                            <div class="flex-1">
                                <p class="mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Current Poster') }}</p>
                                <div class="relative aspect-video max-w-xs overflow-hidden rounded border border-white/10 bg-black">
                                    <img id="current-poster-img" src="{{ $heroPosterPath }}" alt="Current poster" class="absolute inset-0 h-full w-full object-cover">
                                </div>
                                <p class="mt-1.5 break-all text-[11px] text-white/32" id="current-poster-path">{{ basename($heroPosterPath) }}</p>
                            </div>
                            <div class="shrink-0">
                                <button
                                    type="button"
                                    id="generate-poster-btn"
                                    class="inline-flex items-center justify-center gap-2.5 rounded border border-white/14 px-6 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-white/72 transition hover:border-white/35 hover:text-white"
                                >
                                    <svg class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true" id="generate-poster-spinner">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    <span id="generate-poster-label">{{ __('Generate Poster') }}</span>
                                </button>
                                <p class="mt-2 text-[11px] text-white/28" id="generate-poster-status"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Video Preview --}}
                <article class="border border-white/8 bg-[#0d0d13] p-6">
                    <div class="mb-4 flex items-center justify-between gap-4 border-b border-white/8 pb-4">
                        <div>
                            <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Preview') }}</h2>
                            <p class="mt-1 text-xs text-white/35">{{ __('Muted background playback') }}</p>
                        </div>
                    </div>

                    <div class="relative aspect-video overflow-hidden border border-white/10 bg-black">
                        <video
                            id="current-hero-video"
                            class="absolute inset-0 h-full w-full object-cover"
                            autoplay
                            muted
                            loop
                            playsinline
                            preload="auto"
                            crossorigin="anonymous"
                        >
                            <source src="{{ $heroVideoPath }}" type="video/mp4">
                        </video>
                    </div>

                    <p class="mt-4 break-all text-xs leading-6 text-white/38">{{ $heroVideoPath }}</p>
                </article>
            </section>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var fileInput = document.getElementById('hero_video_file');
        var posterDataInput = document.getElementById('hero_poster_data');
        var posterPreviewWrap = document.getElementById('poster-preview-wrap');
        var posterPreviewImg = document.getElementById('poster-preview-img');
        var posterStatus = document.getElementById('poster-status');

        // === Capture first frame when a NEW video file is selected ===
        if (fileInput) {
            fileInput.addEventListener('change', function () {
                var file = this.files[0];
                if (!file || !file.type.startsWith('video/')) return;

                posterPreviewWrap.classList.remove('hidden');
                posterStatus.textContent = 'Extracting first frame…';
                posterStatus.className = 'mt-1.5 text-[11px] text-amber-400/70';
                posterDataInput.value = '';

                captureFirstFrame(URL.createObjectURL(file), function (dataUrl, width, height) {
                    posterPreviewImg.src = dataUrl;
                    posterDataInput.value = dataUrl;
                    posterStatus.textContent = '✓ Poster captured (' + width + '×' + height + ')';
                    posterStatus.className = 'mt-1.5 text-[11px] text-emerald-400/70';
                }, function () {
                    posterStatus.textContent = '⚠ Could not capture frame.';
                    posterStatus.className = 'mt-1.5 text-[11px] text-red-400/70';
                });
            });
        }

        // === Generate poster from CURRENT video ===
        var generateBtn = document.getElementById('generate-poster-btn');
        var generateSpinner = document.getElementById('generate-poster-spinner');
        var generateLabel = document.getElementById('generate-poster-label');
        var generateStatus = document.getElementById('generate-poster-status');
        var currentPosterImg = document.getElementById('current-poster-img');
        var currentPosterPath = document.getElementById('current-poster-path');
        var currentVideo = document.getElementById('current-hero-video');

        if (generateBtn && currentVideo) {
            generateBtn.addEventListener('click', function () {
                generateBtn.disabled = true;
                generateSpinner.classList.remove('hidden');
                generateLabel.textContent = 'Generating…';
                generateStatus.textContent = 'Extracting first frame from current video…';
                generateStatus.className = 'mt-2 text-[11px] text-amber-400/70';

                var videoSrc = currentVideo.querySelector('source').src;

                captureFirstFrame(videoSrc, function (dataUrl, width, height) {
                    // Send to server via AJAX
                    generateStatus.textContent = 'Saving poster…';

                    fetch('{{ route("admin.home.poster") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ poster_data: dataUrl }),
                    })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success) {
                            currentPosterImg.src = data.poster_path + '?t=' + Date.now();
                            currentPosterPath.textContent = data.poster_path.split('/').pop();
                            generateStatus.textContent = '✓ Poster saved (' + width + '×' + height + ')';
                            generateStatus.className = 'mt-2 text-[11px] text-emerald-400/70';
                        } else {
                            generateStatus.textContent = '⚠ ' + (data.error || 'Failed to save.');
                            generateStatus.className = 'mt-2 text-[11px] text-red-400/70';
                        }
                    })
                    .catch(function () {
                        generateStatus.textContent = '⚠ Network error.';
                        generateStatus.className = 'mt-2 text-[11px] text-red-400/70';
                    })
                    .finally(function () {
                        generateBtn.disabled = false;
                        generateSpinner.classList.add('hidden');
                        generateLabel.textContent = 'Generate Poster';
                    });
                }, function () {
                    generateStatus.textContent = '⚠ Could not capture frame from video.';
                    generateStatus.className = 'mt-2 text-[11px] text-red-400/70';
                    generateBtn.disabled = false;
                    generateSpinner.classList.add('hidden');
                    generateLabel.textContent = 'Generate Poster';
                });
            });
        }

        // === Shared: capture first frame from a video URL ===
        function captureFirstFrame(videoUrl, onSuccess, onError) {
            var video = document.createElement('video');
            video.preload = 'auto';
            video.muted = true;
            video.playsInline = true;
            video.crossOrigin = 'anonymous';

            video.addEventListener('loadeddata', function () {
                video.currentTime = 0.1;
            });

            video.addEventListener('seeked', function () {
                try {
                    var canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    var dataUrl = canvas.toDataURL('image/webp', 0.85);
                    onSuccess(dataUrl, canvas.width, canvas.height);
                } catch (e) {
                    if (onError) onError(e);
                }
                video.remove();
            });

            video.addEventListener('error', function () {
                if (onError) onError();
                video.remove();
            });

            video.src = videoUrl;
        }
    });
    </script>
</x-layouts::app>