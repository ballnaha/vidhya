<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Models\Portfolio;
use App\Models\Service;

new #[Title('Portfolio')]
#[Layout('layouts.marketing')]
class extends Component
{
    #[Url(as: 'service', history: true)]
    public $selectedServiceId = '';

    public function selectService($serviceId)
    {
        $this->selectedServiceId = $serviceId;
    }

    public function resetFilters()
    {
        $this->selectedServiceId = '';
    }

    public function render()
    {
        $services = Service::query()->orderBy('sort_order')->get();

        $works = Portfolio::query()
            ->with('service')
            ->where('show_in_portfolio', true)
            ->when($this->selectedServiceId !== '', function ($query) {
                $query->where('service_id', $this->selectedServiceId);
            })
            ->orderBy('sort_order')
            ->get();

        return view('pages.⚡portfolio', [
            'services' => $services,
            'works' => $works,
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
        modalVideoAspectRatio: '16:9',
        modalImageIsPortrait: false,
        openImageModal(src, title) {
            var self = this;
            var probe = new Image();
            probe.onload = function () {
                self.modalImageIsPortrait = probe.naturalHeight > probe.naturalWidth;
                self.modalType = 'image';
                self.modalImage = src;
                self.modalTitle = title;
                self.showModal = true;
            };
            probe.onerror = function () {
                self.modalImageIsPortrait = false;
                self.modalType = 'image';
                self.modalImage = src;
                self.modalTitle = title;
                self.showModal = true;
            };
            probe.src = src;
        },
        getEmbedUrl(url) {
            if (!url) return '';
            var isYoutube = url.indexOf('youtube.com') > -1 || url.indexOf('youtu.be') > -1;
            var isVimeo = url.indexOf('vimeo.com') > -1;

            if (isYoutube) {
                var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|shorts\/|watch\?v=|\&v=)([^#\&\?]*).*/;
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
    <section class="relative isolate overflow-hidden px-6 pb-24 pt-40 sm:px-10 lg:px-20">
        <img
            src="/images/bg_portfolio.webp"
            alt=""
            class="pointer-events-none absolute inset-0 -z-30 h-full w-full object-cover object-center"
            fetchpriority="high"
            decoding="async"
            aria-hidden="true"
        >
        <div class="pointer-events-none absolute inset-0 -z-20 bg-[linear-gradient(90deg,rgba(5,5,7,0.92)_0%,rgba(5,5,7,0.66)_48%,rgba(5,5,7,0.84)_100%)]"></div>
        <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_15%_85%,rgba(54,107,195,0.25)_0%,transparent_58%)]"></div>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 -z-10 h-32 bg-linear-to-t from-[#0a0a0c] to-transparent"></div>
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
    <section class="px-6 py-12 sm:px-10 lg:px-20 bg-[#0a0a0c]">
        <div class="mx-auto max-w-[1800px] space-y-12">
 
            <!-- Filters Bar -->
            <div class="border-b border-white/5 pb-10" data-reveal>
                <!-- Service Filters -->
                <div class="space-y-4 mb-8">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-[#366bc3]">Filter by Service Category</p>
                    <div class="flex flex-wrap gap-3">
                        <button 
                            type="button" 
                            wire:click="selectService('')" 
                            class="rounded border px-7 py-3 text-xs font-bold uppercase tracking-[0.12em] transition-all duration-300 {{ $selectedServiceId === '' ? 'border-[#366bc3] bg-[#366bc3]/10 text-white shadow-lg shadow-[#366bc3]/15' : 'border-white/10 text-white/42 hover:border-white/25 hover:text-white hover:bg-white/[0.02]' }}"
                        >
                            All Services
                        </button>
                        @foreach ($services as $service)
                            @php
                                $isActive = (string)$selectedServiceId === (string)$service->id;
                            @endphp
                            <button 
                                type="button" 
                                wire:click="selectService('{{ $service->id }}')" 
                                class="rounded border px-7 py-3 text-xs font-bold uppercase tracking-[0.12em] transition-all duration-300 {{ $isActive ? 'text-white' : 'border-white/10 text-white/42 hover:border-white/25 hover:text-white hover:bg-white/[0.02]' }}"
                                style="{{ $isActive ? 'border-color: ' . $service->accent . '; background-color: ' . $service->accent . '18; box-shadow: 0 10px 15px -3px ' . $service->accent . '20;' : '' }}"
                            >
                                {{ $service->title }}
                            </button>
                        @endforeach
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
                @php
                    $workGroups = [
                        [
                            'key' => 'video',
                            'eyebrow' => 'Motion',
                            'title' => 'Films & Video',
                            'description' => 'Director-led films, commercials, and moving-image stories.',
                            'accent' => '#e60012',
                            'works' => $works->filter(fn ($work) => filled($work->video_url))->values(),
                        ],
                        [
                            'key' => 'still',
                            'eyebrow' => 'Visuals',
                            'title' => 'Images & Stills',
                            'description' => 'Crafted key visuals, campaign imagery, and cinematic stills.',
                            'accent' => '#366bc3',
                            'works' => $works->filter(fn ($work) => blank($work->video_url))->values(),
                        ],
                    ];
                    $visibleGroupNumber = 0;
                @endphp

                <div class="space-y-24 lg:space-y-32">
                    @foreach ($workGroups as $group)
                        @continue($group['works']->isEmpty())
                        @php
                            $visibleGroupNumber++;
                        @endphp

                        <section wire:key="portfolio-group-{{ $group['key'] }}" class="space-y-8">
                            <div class="flex flex-col gap-5 border-b border-white/8 pb-6 sm:flex-row sm:items-end sm:justify-between" data-reveal>
                                <div class="flex items-start gap-4 sm:gap-6">
                                    <span class="pt-1 font-mono text-[11px] tracking-[0.2em] text-white/25">{{ str_pad((string) $visibleGroupNumber, 2, '0', STR_PAD_LEFT) }}</span>
                                    <div>
                                        <p class="mb-2 text-[10px] font-bold uppercase tracking-[0.24em]" style="color: {{ $group['accent'] }}">{{ $group['eyebrow'] }}</p>
                                        <h2 class="text-[clamp(1.75rem,4vw,3rem)] font-black uppercase leading-none tracking-[-0.025em] text-white">{{ $group['title'] }}</h2>
                                    </div>
                                </div>
                                <div class="sm:max-w-sm sm:text-right">
                                    <p class="text-sm leading-6 text-white/40">{{ $group['description'] }}</p>
                                    <p class="mt-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/25">
                                        {{ $group['works']->count() }} {{ Str::plural('project', $group['works']->count()) }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid gap-6 md:grid-cols-6">
                    @foreach ($group['works'] as $index => $work)
                        @php
                            $delay = ($index % 3) * 100;
                            $spanClass = match ($work->span) {
                                'md:col-span-3' => 'md:col-span-3',
                                'md:col-span-4' => 'md:col-span-4',
                                'md:col-span-6' => 'md:col-span-6',
                                default => 'md:col-span-2',
                            };
                        @endphp
                        <div 
                            class="home-animated-card {{ $spanClass }} group relative aspect-video overflow-hidden bg-black border border-white/10 hover:border-[#366bc3]/30 hover:shadow-lg hover:shadow-[#366bc3]/5 transition-all duration-500"
                            style="--reveal-delay: {{ $delay }}ms;"
                            data-reveal
                        >
                            <button
                                type="button"
                                class="w-full h-full text-left relative block"
                                @click="
                                    @if(!empty($work->video_url))
                                        modalTitle = '{{ addslashes($work->title) }}';
                                        modalVideoAspectRatio = '{{ $work->video_aspect_ratio ?? '16:9' }}';
                                        modalType = 'video';
                                        modalVideoUrl = '{{ $work->video_url }}';
                                        showModal = true;
                                    @else
                                        openImageModal('{{ $work->image }}', '{{ addslashes($work->title) }}');
                                    @endif
                                "
                            >
                                <!-- Blurred backdrop fills the frame behind portrait covers -->
                                <img src="{{ $work->image }}" alt="" aria-hidden="true" class="absolute inset-0 h-full w-full scale-110 object-cover blur-sm opacity-60" loading="lazy">
                                <img src="{{ $work->image }}" alt="{{ $work->title }}" class="relative h-full w-full object-contain transition duration-500 group-hover:scale-105" loading="lazy">
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
 
                                <!-- Text Overlay (Title) -->
                                <div class="absolute inset-x-0 bottom-0 p-5 space-y-1">
                                    @if($work->service)
                                        <p class="text-[9px] font-black uppercase tracking-[0.15em] mb-1" style="color: {{ $work->service->accent }}">{{ $work->service->title }}</p>
                                    @endif
                                    <h3 class="text-sm font-black uppercase tracking-[0.02em] text-white">{{ $work->title }}</h3>
                                </div>
                            </button>
                        </div>
                    @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            @endif
 
        </div>
    </section>
 
    <style>
        @media (max-width: 640px) {
            .portfolio-lightbox-box {
                width: 100% !important;
                height: calc(100dvh - 132px) !important;
                max-height: calc(100dvh - 132px) !important;
                aspect-ratio: auto !important;
                border-radius: 0 !important;
                border: none !important;
            }
        }
    </style>

    <!-- Global Premium Unified Lightbox Modal -->
    <div
        class="fixed inset-0 z-[110] flex flex-col justify-center items-center bg-black/95 p-0 backdrop-blur-md sm:px-8 sm:py-6"
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
        <div class="relative w-full h-full sm:h-auto max-w-[1200px] flex flex-col items-center justify-center gap-3" @click.stop>
            <!-- Close Button Bar: kept out of the video frame so it never
                 overlaps the YouTube/Vimeo player's own on-screen controls. -->
            <div class="flex w-full justify-end px-1 sm:px-0">
                <button
                    type="button"
                    class="grid size-10 place-items-center rounded-full border border-white/20 bg-black/72 text-2xl font-light text-white/70 transition hover:border-white/40 hover:bg-black/90 hover:text-white"
                    @click="showModal = false; modalVideoUrl = ''"
                    aria-label="Close lightbox"
                >×</button>
            </div>

            <!-- Content Wrapper -->
            <div
                class="portfolio-lightbox-box relative overflow-hidden bg-black shadow-2xl border border-white/10 rounded-lg sm:max-h-[80vh] flex items-center justify-center"
                :style="modalType === 'video' ? {
                    aspectRatio: modalVideoAspectRatio === '9:16' ? '9 / 16' : '16 / 9',
                    width: modalVideoAspectRatio === '9:16' ? 'min(100%, 45vh)' : '100%'
                } : {
                    aspectRatio: modalImageIsPortrait ? '9 / 16' : '16 / 9',
                    width: modalImageIsPortrait ? 'min(100%, 45vh)' : '100%'
                }"
            >

                <!-- Image Lightbox -->
                <div x-show="modalType === 'image'" class="h-full w-full flex items-center justify-center">
                    <img :src="modalImage" :alt="modalTitle" class="h-full w-full object-contain">
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
