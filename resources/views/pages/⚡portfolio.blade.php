<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Director;

new #[Title('Portfolio')]
#[Layout('layouts.marketing')]
class extends Component
{
    public $selectedCategory = '';
    public $selectedType = ''; // '', 'video', 'image'

    public function selectCategory($category)
    {
        $this->selectedCategory = $category;
    }

    public function selectType($type)
    {
        $this->selectedType = $type;
    }

    public function resetFilters()
    {
        $this->selectedCategory = '';
        $this->selectedType = '';
    }

    public function render()
    {
        // Fetch all directors
        $directors = Director::all();

        // Collate all works dynamically
        $allWorks = collect();
        foreach ($directors as $director) {
            if ($director->works && is_array($director->works)) {
                foreach ($director->works as $work) {
                    // Check if the work should be shown on the portfolio page
                    $showInPortfolio = isset($work['show_in_portfolio']) ? (bool) $work['show_in_portfolio'] : true;
                    if (!$showInPortfolio) {
                        continue;
                    }

                    if ($director->slug === 'general') {
                        $work['director_name'] = 'Vidhya Studio';
                    } else {
                        $work['director_name'] = $director->first_name . ' ' . $director->last_name;
                    }
                    $work['director_slug'] = $director->slug;
                    $allWorks->push((object) $work);
                }
            }
        }

        // Extract unique categories dynamically from works
        $categories = $allWorks->pluck('title')->unique()->values()->all();

        // Apply filters
        $filteredWorks = $allWorks
            ->when($this->selectedCategory !== '', function ($collection) {
                return $collection->where('title', $this->selectedCategory);
            })
            ->when($this->selectedType === 'video', function ($collection) {
                return $collection->filter(fn($work) => !empty($work->video_url));
            })
            ->when($this->selectedType === 'image', function ($collection) {
                return $collection->filter(fn($work) => empty($work->video_url));
            });

        return view('pages.⚡portfolio', [
            'categories' => $categories,
            'works' => $filteredWorks,
        ]);
    }
};
?>

<main class="bg-[#0a0a0c] text-white" 
    x-data="{ 
        showModal: false, 
        modalType: 'image', 
        modalImage: '', 
        modalTitle: '', 
        modalVideoUrl: '',
        getEmbedUrl(url) {
            if (!url) return '';
            var isYoutube = url.indexOf('youtube.com') > -1 || url.indexOf('youtu.be') > -1;
            var isVimeo = url.indexOf('vimeo.com') > -1;

            if (isYoutube) {
                var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                var match = url.match(regExp);
                if (match && match[2].length === 11) {
                    return 'https://www.youtube.com/embed/' + match[2] + '?autoplay=1';
                }
            } else if (isVimeo) {
                var regExp = /vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/;
                var match = url.match(regExp);
                if (match) {
                    return 'https://player.vimeo.com/video/' + match[1] + '?autoplay=1';
                }
            }
            return url;
        },
        isIframeUrl(url) {
            if (!url) return false;
            return url.indexOf('youtube.com') > -1 || url.indexOf('youtu.be') > -1 || url.indexOf('vimeo.com') > -1;
        }
    }" 
    @keydown.escape.window="showModal = false; modalVideoUrl = ''"
>
    <!-- Hero Section -->
    <section class="relative overflow-hidden px-6 pb-24 pt-40 sm:px-10 lg:px-20"
        style="background: radial-gradient(ellipse at 15% 85%, rgba(54,107,195,0.18) 0%, #0a0a0c 60%);">
        <div
            class="pointer-events-none absolute -right-15 -top-15 h-[400px] w-[400px] bg-[radial-gradient(ellipse,rgba(54,107,195,0.13)_0%,transparent_65%)]">
        </div>
        <div class="relative z-10 mx-auto max-w-[1800px]" data-reveal>
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.26em] text-white/35">Our Creative Output</p>
            <h1
                class="max-w-none text-[clamp(3rem,6.4vw,5.35rem)] font-black uppercase leading-none tracking-[-0.03em]">
                <span
                    class="bg-linear-to-r from-[#366bc3] via-[#6d55a5] to-[#823665] bg-clip-text text-transparent">Cinematic
                </span><span
                    class="bg-linear-to-r from-[#823665] via-[#b4143c] to-[#e60012] bg-clip-text text-transparent">Portfolio</span>
            </h1>
            <p class="mt-6 max-w-2xl text-[17px] font-normal leading-[1.8] text-white/48">Explore our showcase of
                premium, director-led AI cinematic outputs, visual storytelling, and commercials delivered with speed,
                scale, and soul.</p>
        </div>
    </section>
 
    <!-- Portfolio Section -->
    <section class="px-6 py-12 sm:px-10 lg:px-20 bg-black">
        <div class="mx-auto max-w-[1800px] space-y-12">
 
            <!-- Filters Bar -->
            <div class="border-b border-white/5 pb-10" data-reveal>
                <!-- Type Filters (Videos / Images) -->
                <div class="space-y-4">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-[#e60012]">Filter by Media Type</p>
                    <div class="flex flex-wrap gap-3">
                        <button 
                            type="button" 
                            wire:click="selectType('')" 
                            class="rounded border px-7 py-3 text-xs font-bold uppercase tracking-[0.12em] transition-all duration-300 {{ $selectedType === '' ? 'border-[#366bc3] bg-[#366bc3]/10 text-white shadow-lg shadow-[#366bc3]/15' : 'border-white/10 text-white/42 hover:border-white/25 hover:text-white hover:bg-white/[0.02]' }}"
                        >
                            All Outputs
                        </button>
                        <button 
                            type="button" 
                            wire:click="selectType('video')" 
                            class="inline-flex items-center gap-2 rounded border px-7 py-3 text-xs font-bold uppercase tracking-[0.12em] transition-all duration-300 {{ $selectedType === 'video' ? 'border-[#e60012] bg-[#e60012]/10 text-white shadow-lg shadow-[#e60012]/15' : 'border-white/10 text-white/42 hover:border-white/25 hover:text-white hover:bg-white/[0.02]' }}"
                        >
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Cinematic Videos
                        </button>
                        <button 
                            type="button" 
                            wire:click="selectType('image')" 
                            class="inline-flex items-center gap-2 rounded border px-7 py-3 text-xs font-bold uppercase tracking-[0.12em] transition-all duration-300 {{ $selectedType === 'image' ? 'border-[#823665] bg-[#823665]/10 text-white shadow-lg shadow-[#823665]/15' : 'border-white/10 text-white/42 hover:border-white/25 hover:text-white hover:bg-white/[0.02]' }}"
                        >
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Visual Stills / Photos
                        </button>
                    </div>
                </div>
            </div>

            <!-- Works Grid -->
            @if ($works->isEmpty())
                <div class="text-center py-24 border border-white/5 bg-[#0a0a0c] rounded" data-reveal>
                    <div class="inline-flex items-center justify-center size-16 rounded-full bg-white/[0.03] border border-white/10 mb-4">
                        <svg class="h-8 w-8 text-white/35" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">No projects found</h3>
                    <p class="text-sm text-white/45 mb-6 font-normal">We couldn't find any portfolio items matching the selected filters.</p>
                    <button 
                        type="button" 
                        wire:click="resetFilters" 
                        class="inline-flex rounded px-6 py-2.5 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110" 
                        style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);"
                    >
                        Reset Filters
                    </button>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($works as $index => $work)
                        @php
                            $delay = ($index % 3) * 100;
                        @endphp
                        <div 
                            class="home-animated-card group relative aspect-video overflow-hidden bg-black border border-white/10 hover:border-[#366bc3]/30 hover:shadow-lg hover:shadow-[#366bc3]/5 transition-all duration-500"
                            style="--reveal-delay: {{ $delay }}ms;"
                            data-reveal
                        >
                            <button
                                type="button"
                                class="w-full h-full text-left relative block"
                                @click="
                                    modalTitle = '{{ addslashes($work->title) }} (by {{ addslashes($work->director_name) }})';
                                    @if(!empty($work->video_url))
                                        modalType = 'video';
                                        modalVideoUrl = '{{ $work->video_url }}';
                                    @else
                                        modalType = 'image';
                                        modalImage = '{{ $work->image }}';
                                    @endif
                                    showModal = true;
                                "
                            >
                                <img src="{{ $work->image }}" alt="{{ $work->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                <div class="absolute inset-0 bg-linear-to-t from-black/85 via-black/20 to-transparent transition-all duration-300 group-hover:from-black/90"></div>
                                
                                <!-- Type Badge -->
                                <span class="absolute top-4 right-4 z-10 rounded-full px-2.5 py-1 text-[8px] font-bold uppercase tracking-widest border backdrop-blur-md transition duration-300 {{ !empty($work->video_url) ? 'border-[#e60012]/30 bg-black/45 text-[#e60012]' : 'border-[#366bc3]/30 bg-black/45 text-[#366bc3]' }}">
                                    {{ !empty($work->video_url) ? 'Video' : 'Still' }}
                                </span>

                                @if(!empty($work->video_url))
                                    <!-- Play Button Overlay -->
                                    <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 grid size-10 place-items-center rounded bg-black/72 text-white shadow-lg shadow-black/30 opacity-0 scale-90 transition-all duration-300 group-hover:opacity-100 group-hover:scale-110" aria-hidden="true">
                                        <span class="ml-0.5 h-0 w-0 border-y-[7px] border-l-[10px] border-y-transparent border-l-white"></span>
                                    </span>
                                @else
                                    <!-- Zoom Icon Overlay -->
                                    <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 grid size-10 place-items-center rounded bg-black/72 text-white shadow-lg shadow-black/30 opacity-0 scale-90 transition-all duration-300 group-hover:opacity-100 group-hover:scale-110" aria-hidden="true">
                                        <svg class="size-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                    </span>
                                @endif
 
                                <!-- Text Overlay (Director and Title) -->
                                <div class="absolute inset-x-0 bottom-0 p-5 space-y-1">
                                    <p class="text-[9px] font-semibold uppercase tracking-[0.2em] text-[#e60012]">{{ $work->director_name }}</p>
                                    <h3 class="text-sm font-black uppercase tracking-[0.02em] text-white">{{ $work->title }}</h3>
                                </div>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
 
        </div>
    </section>
 
    <!-- Global Premium Unified Lightbox Modal -->
    <div 
        class="fixed inset-0 z-[110] flex flex-col justify-center items-center bg-black/95 px-4 py-6 backdrop-blur-md sm:px-8" 
        x-show="showModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click="showModal = false; modalVideoUrl = ''"
        x-cloak
    >
        <!-- Close Button -->
        <button 
            type="button" 
            class="absolute top-4 right-4 sm:top-6 sm:right-6 z-50 grid size-10 place-items-center rounded-full border border-white/20 bg-black/72 text-2xl font-light text-white/70 transition hover:border-white/40 hover:bg-black/90 hover:text-white" 
            @click="showModal = false; modalVideoUrl = ''" 
            aria-label="Close lightbox"
        >×</button>
        
        <div class="relative w-full max-w-[1200px] flex flex-col items-center gap-4" @click.stop>
            <!-- Content Wrapper -->
            <div class="relative overflow-hidden bg-black shadow-2xl border border-white/10 rounded-lg w-full max-h-[80vh] flex items-center justify-center" :class="modalType === 'video' ? 'aspect-video' : ''">
                
                <!-- Image Lightbox -->
                <div x-show="modalType === 'image'" class="h-full w-full flex items-center justify-center">
                    <img :src="modalImage" :alt="modalTitle" class="max-h-[80vh] max-w-full object-contain">
                </div>

                <!-- Video Lightbox (YouTube/Vimeo Iframe) -->
                <div x-show="modalType === 'video' && isIframeUrl(modalVideoUrl)" class="absolute inset-0 h-full w-full">
                    <template x-if="modalType === 'video' && isIframeUrl(modalVideoUrl)">
                        <iframe :src="getEmbedUrl(modalVideoUrl)" class="absolute inset-0 h-full w-full" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                    </template>
                </div>

                <!-- Video Lightbox (HTML5 Native Video player) -->
                <div x-show="modalType === 'video' && !isIframeUrl(modalVideoUrl)" class="h-full w-full flex items-center justify-center">
                    <template x-if="modalType === 'video' && !isIframeUrl(modalVideoUrl)">
                        <video :src="modalVideoUrl" class="h-full w-full object-contain" controls autoplay></video>
                    </template>
                </div>

            </div>
            
            <!-- Title -->
            <div class="text-center px-4">
                <h4 class="text-base font-bold uppercase tracking-wider text-white" x-text="modalTitle"></h4>
            </div>
        </div>
    </div>
</main>
