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

            {{-- Section 1: Hero Background Setup --}}
            <section class="space-y-4">
                <div class="border-b border-white/8 pb-3">
                    <h2 class="text-base font-black uppercase tracking-[0.05em] text-white">{{ __('1. Homepage Hero Background Video (MP4)') }}</h2>
                    <p class="mt-1 text-xs text-white/45">{{ __('Upload and preview the hosted MP4 file that loops silently as the background of the home hero section.') }}</p>
                </div>

                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(320px,520px)]">
                    <div class="space-y-6">
                        {{-- Upload Video Form --}}
                        <form method="POST" action="{{ route('admin.home.update') }}" enctype="multipart/form-data" class="border border-white/8 bg-[#0d0d13] p-6" data-admin-home-form>
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="hero_poster_data" id="hero_poster_data" value="">

                            <div class="mb-6 border-b border-white/8 pb-4">
                                <h3 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Upload Video') }}</h3>
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
                                <h3 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Poster Image') }}</h3>
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
                    <article class="border border-white/8 bg-[#0d0d13] p-6 h-fit">
                        <div class="mb-4 flex items-center justify-between gap-4 border-b border-white/8 pb-4">
                            <div>
                                <h3 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Preview Background Video') }}</h3>
                                <p class="mt-1 text-xs text-white/35">{{ __('Muted background loop on the website') }}</p>
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

                        <p class="mt-4 break-all text-xs leading-6 text-white/38 font-mono">{{ $heroVideoPath }}</p>
                    </article>
                </div>
            </section>

            {{-- Section 2: Watch the Reel settings --}}
            <section class="space-y-4 pt-6 border-t border-white/10">
                <div class="border-b border-white/8 pb-3">
                    <h2 class="text-base font-black uppercase tracking-[0.05em] text-white pt-5">{{ __('2. Homepage "Watch the Reel" Video (YouTube)') }}</h2>
                    <p class="mt-1 text-xs text-white/45">{{ __('Configure the YouTube video URL that pops up in a lightbox when visitors click the "Watch the reel" button.') }}</p>
                </div>

                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(320px,520px)]">
                    {{-- YouTube Reel URL Form --}}
                    <form method="POST" action="{{ route('admin.home.youtube') }}" class="border border-white/8 bg-[#0d0d13] p-6 h-fit" data-admin-home-form>
                        @csrf
                        @method('PATCH')

                        <div class="mb-6 border-b border-white/8 pb-4">
                            <h3 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('YouTube Settings') }}</h3>
                            <p class="mt-2 text-xs leading-6 text-white/38">{{ __('Specify the YouTube URL of the video shown when the "Watch the reel" button is clicked.') }}</p>
                        </div>

                        <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider" for="hero_youtube_url">{{ __('YouTube URL') }}</label>
                        <div class="relative">
                            <input
                                id="hero_youtube_url"
                                name="hero_youtube_url"
                                type="url"
                                value="{{ old('hero_youtube_url', $heroYoutubeUrl) }}"
                                placeholder="https://www.youtube.com/watch?v=..."
                                class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]"
                            >
                        </div>
                        <p class="mt-2 text-[11px] text-white/28">{{ __('Accepts standard youtube.com/watch?v=..., youtu.be/..., shorts/..., or embed/... URLs.') }}</p>
                        @error('hero_youtube_url')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror

                        <div class="mt-6 flex items-center justify-between border-t border-white/8 pt-5">
                            <p class="text-[11px] text-white/32">{{ __('Current ID') }}: <span class="font-mono text-white/58">{{ App\Models\SiteSetting::youtubeIdFromUrl($heroYoutubeUrl) }}</span></p>
                            <button type="submit" class="inline-flex items-center justify-center gap-3 rounded px-8 py-3.5 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-home-save>
                                <svg class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true" data-admin-home-spinner>
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span data-admin-home-save-label>{{ __('Save URL') }}</span>
                            </button>
                        </div>
                    </form>

                    {{-- YouTube Reel Preview --}}
                    <article class="border border-white/8 bg-[#0d0d13] p-6 h-fit">
                        <div class="mb-4 flex items-center justify-between gap-4 border-b border-white/8 pb-4">
                            <div>
                                <h3 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('YouTube Reel Preview') }}</h3>
                                <p class="mt-1 text-xs text-white/35">{{ __('Thumbnail of the configured video') }}</p>
                            </div>
                        </div>

                        @php
                            $youtubeId = App\Models\SiteSetting::youtubeIdFromUrl($heroYoutubeUrl);
                        @endphp

                        @if ($youtubeId)
                            <div class="relative aspect-video overflow-hidden border border-white/10 bg-black rounded">
                                <img 
                                    src="https://img.youtube.com/vi/{{ $youtubeId }}/maxresdefault.jpg" 
                                    onerror="this.src='https://img.youtube.com/vi/{{ $youtubeId }}/mqdefault.jpg'" 
                                    alt="YouTube Thumbnail" 
                                    class="absolute inset-0 h-full w-full object-cover"
                                >
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                    <div class="grid size-12 place-items-center rounded bg-black/70 text-white shadow-lg border border-white/10">
                                        <svg class="size-6" viewBox="0 0 24 24" aria-hidden="true" style="fill: #ffffff; color: #ffffff;">
                                            <path d="M8 5v14l11-7z" fill="#ffffff"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-4 text-xs leading-6 text-white/38">
                                {{ __('Video ID') }}: <span class="font-mono text-emerald-400 font-semibold">{{ $youtubeId }}</span>
                            </p>
                        @else
                            <div class="relative aspect-video overflow-hidden border border-white/10 bg-[#07070a] rounded flex flex-col items-center justify-center text-center p-4">
                                <p class="text-xs text-white/35">{{ __('Invalid or empty YouTube Reel URL') }}</p>
                            </div>
                        @endif
                    </article>
                </div>
            </section>

            {{-- Section 3: Social Media Links settings --}}
            <section class="space-y-4 pt-6 border-t border-white/10">
                <div class="border-b border-white/8 pb-3">
                    <h2 class="text-base font-black uppercase tracking-[0.05em] text-white pt-5">{{ __('3. Homepage Social Media Links') }}</h2>
                    <p class="mt-1 text-xs text-white/45">{{ __('Configure dynamic social media links displayed as circles next to the "Our services" button on the homepage.') }}</p>
                </div>

                <div class="grid gap-6">
                    <form 
                        method="POST" 
                        action="{{ route('admin.home.social-links') }}" 
                        enctype="multipart/form-data" 
                        class="border border-white/8 bg-[#0d0d13] p-6 w-full"
                        data-admin-home-form
                        x-data="{ 
                            socials: {{ json_encode(SiteSetting::socialLinks()) }},
                            addSocial() {
                                this.socials.push({ name: '', url: '', type: 'svg', icon_svg: '', icon_path: '', id: Date.now() });
                            },
                            removeSocial(index) {
                                this.socials.splice(index, 1);
                            }
                        }"
                    >
                        @csrf
                        @method('PATCH')

                        <div class="mb-6 border-b border-white/8 pb-4 flex justify-between items-end">
                            <div>
                                <h3 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Manage Links') }}</h3>
                                <p class="mt-1 text-xs text-white/38">{{ __('Create and modify links with custom SVG vector graphics or uploaded image icons.') }}</p>
                            </div>
                            <button 
                                type="button" 
                                @click="addSocial()" 
                                class="inline-flex rounded border border-white/14 px-4 py-2 text-xs font-semibold uppercase tracking-[0.08em] text-white/72 transition hover:border-white/35 hover:text-white cursor-pointer"
                            >
                                {{ __('+ Add Link') }}
                            </button>
                        </div>

                        <!-- Empty state -->
                        <div x-show="socials.length === 0" class="py-8 text-center border border-dashed border-white/10 rounded text-xs text-white/28">
                            {{ __('No social links added yet. Click "+ Add Link" to create one.') }}
                        </div>

                        <!-- Dynamic list -->
                        <div class="space-y-4">
                            <template x-for="(social, index) in socials" :key="social.id || index">
                                <div 
                                    class="border border-white/8 bg-black/20 rounded overflow-hidden"
                                    x-data="{ isOpen: false }"
                                    x-init="if (social.isOpen) isOpen = true"
                                >
                                    <!-- Accordion Header -->
                                    <div 
                                        class="p-4 bg-white/[0.02] flex items-center justify-between cursor-pointer select-none hover:bg-white/[0.04] transition"
                                        @click="isOpen = !isOpen"
                                    >
                                        <div class="flex items-center gap-3">
                                            <!-- Rotate arrow icon -->
                                            <span class="text-[10px] text-white/40 transition-transform duration-200" :class="isOpen ? 'rotate-90' : ''">▶</span>
                                            <span class="text-xs font-bold uppercase tracking-wider text-white" x-text="social.name || '{{ __('New Link') }}'"></span>
                                            <span 
                                                class="rounded px-2.5 py-0.5 text-[8px] font-bold uppercase tracking-widest border backdrop-blur-md transition duration-300"
                                                :class="social.type === 'svg' ? 'border-[#366bc3]/30 bg-black/45 text-[#366bc3]' : 'border-[#e60012]/30 bg-black/45 text-[#e60012]'"
                                                x-text="social.type === 'svg' ? 'SVG' : 'Image'"
                                                x-show="social.name"
                                            ></span>
                                        </div>

                                        <div class="flex items-center gap-4">
                                            <button 
                                                type="button" 
                                                @click.stop="removeSocial(index)" 
                                                class="text-xs font-semibold text-red-400/70 hover:text-red-400 transition cursor-pointer"
                                                title="{{ __('Remove link') }}"
                                            >
                                                {{ __('Remove') }}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Accordion Content -->
                                    <div 
                                        class="p-5 border-t border-white/6 space-y-4 bg-black/40"
                                        x-show="isOpen"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 -translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                    >
                                        <div class="grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="block mb-1.5 text-[11px] font-semibold text-white/45 uppercase tracking-wider">{{ __('Platform Name') }}</label>
                                                <input 
                                                    type="text" 
                                                    :name="'socials[' + index + '][name]'" 
                                                    x-model="social.name" 
                                                    placeholder="e.g. Instagram"
                                                    class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]"
                                                    required
                                                >
                                            </div>
                                            <div>
                                                <label class="block mb-1.5 text-[11px] font-semibold text-white/45 uppercase tracking-wider">{{ __('Link URL') }}</label>
                                                <input 
                                                    type="url" 
                                                    :name="'socials[' + index + '][url]'" 
                                                    x-model="social.url" 
                                                    placeholder="https://..."
                                                    class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]"
                                                    required
                                                >
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block mb-1.5 text-[11px] font-semibold text-white/45 uppercase tracking-wider">{{ __('Icon Type') }}</label>
                                            <div class="flex gap-6 items-center">
                                                <label class="inline-flex items-center gap-2 text-xs text-white/60 cursor-pointer">
                                                    <input type="radio" :name="'socials[' + index + '][type]'" value="svg" x-model="social.type" class="accent-[#366bc3]">
                                                    <span>{{ __('SVG Code (Inline)') }}</span>
                                                </label>
                                                <label class="inline-flex items-center gap-2 text-xs text-white/60 cursor-pointer">
                                                    <input type="radio" :name="'socials[' + index + '][type]'" value="image" x-model="social.type" class="accent-[#366bc3]">
                                                    <span>{{ __('Image File Upload') }}</span>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- SVG Code input -->
                                        <div x-show="social.type === 'svg'" class="space-y-1">
                                            <label class="block mb-1.5 text-[11px] font-semibold text-white/45 uppercase tracking-wider">{{ __('SVG Vector HTML Markup') }}</label>
                                            <textarea 
                                                :name="'socials[' + index + '][icon_svg]'" 
                                                x-model="social.icon_svg" 
                                                rows="3" 
                                                placeholder="&lt;svg viewBox=&quot;0 0 24 24&quot; class=&quot;size-5&quot;&gt;&lt;path d=&quot;...&quot; /&gt;&lt;/svg&gt;"
                                                class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-2.5 text-xs font-mono text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]"
                                            ></textarea>
                                            <p class="text-[10px] text-white/28">{{ __('Paste raw <svg> tag. Standard size: size-5. Keep fill/stroke set to currentColor.') }}</p>
                                        </div>

                                        <!-- Image File Upload option -->
                                        <div x-show="social.type === 'image'" class="space-y-2">
                                            <label class="block mb-1.5 text-[11px] font-semibold text-white/45 uppercase tracking-wider">{{ __('Icon Image File') }}</label>
                                            <input type="hidden" :name="'socials[' + index + '][existing_icon_path]'" x-model="social.icon_path">
                                            
                                            <div class="flex items-center gap-4">
                                                <input 
                                                    type="file" 
                                                    :name="'socials[' + index + '][icon_file]'" 
                                                    accept="image/*"
                                                    class="text-xs text-white/50 file:mr-3 file:rounded file:border-0 file:bg-white/10 file:px-3 file:py-1 file:text-[11px] file:font-semibold file:uppercase file:text-white/70 hover:file:bg-white/20"
                                                >
                                                
                                                <!-- Existing preview -->
                                                <div x-show="social.icon_path" class="flex items-center gap-2 border border-white/10 bg-black/40 p-1.5 rounded">
                                                    <img :src="social.icon_path" class="size-6 object-contain" alt="Preview">
                                                    <span class="text-[10px] text-white/45" x-text="social.icon_path.split('/').pop()"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-6 flex justify-end border-t border-white/8 pt-5">
                            <button type="submit" class="inline-flex items-center justify-center gap-3 rounded px-8 py-3.5 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-home-save>
                                <svg class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true" data-admin-home-spinner>
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span data-admin-home-save-label>{{ __('Save Social Links') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
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