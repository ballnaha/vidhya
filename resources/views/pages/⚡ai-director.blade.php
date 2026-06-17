<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Director;

new #[Title('AI Director - Sunil Thomas')]
#[Layout('layouts.marketing')]
class extends Component
{
    public function render()
    {
        return view('pages.⚡ai-director', [
            'directors' => Director::where('slug', '!=', 'general')->get(),
        ]);
    }
};
?>

<main class="bg-[#0a0a0c] text-white" data-director-tabs>
    @if ($directors->isEmpty())
        <!-- Premium Empty State Section -->
        <section class="relative overflow-hidden px-6 pt-36 pb-24 sm:px-10 lg:px-20 flex flex-col items-center justify-start min-h-[60vh]" style="background: radial-gradient(ellipse at center, rgba(54,107,195,0.08) 0%, #0a0a0c 70%);">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(54,107,195,0.05)_0%,transparent_65%)]"></div>
            
            <div class="relative z-10 text-center max-w-2xl space-y-6" data-reveal>
                <!-- Glowing Icon container -->
                <div class="inline-flex items-center justify-center size-20 rounded-2xl bg-white/[0.02] border border-white/10 shadow-2xl shadow-[#366bc3]/5 relative group overflow-hidden mb-4">
                    <div class="absolute inset-0 bg-linear-to-r from-[#366bc3]/20 to-[#e60012]/20 opacity-0 group-hover:opacity-100 transition duration-500"></div>
                    <svg class="h-9 w-9 text-white/35 group-hover:text-white transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                
                <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-[#e60012]">{{ __('Vidhya AI Director Roster') }}</p>
                
                <h1 class="text-[clamp(2rem,5vw,3.5rem)] font-black uppercase leading-tight tracking-[-0.03em] bg-linear-to-r from-white via-white to-white/60 bg-clip-text text-transparent">
                    {{ __('Cinematic Profiles Coming Soon') }}
                </h1>
                
                <p class="text-sm leading-relaxed text-white/45 font-medium max-w-lg mx-auto">
                    {{ __('We are currently curating and preparing the showcase profiles of our elite AI Directors. Check back soon to explore their visionary workflows and cinematic masterpieces.') }}
                </p>
            </div>
        </section>
    @else
        <section class="sticky top-[72px] z-40 border-y border-white/8 bg-[#0a0a0c]/92 px-6 py-3 backdrop-blur-xl sm:px-10 lg:px-20" data-director-tabs-selector>
            <div class="mx-auto flex max-w-[1800px] gap-2 overflow-x-auto">
                @foreach ($directors as $director)
                    <button
                        type="button"
                        class="shrink-0 rounded border px-5 py-3 text-[11px] font-bold uppercase tracking-[0.12em] transition data-[active]:border-[#366bc3] data-[active]:bg-white/8 data-[active]:text-white border-white/10 text-white/42 hover:border-white/25 hover:text-white"
                        data-director-tab="{{ $director['slug'] }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                    >
                        {{ $director['first_name'] }} {{ $director['last_name'] }}
                    </button>
                @endforeach
            </div>
        </section>

        @foreach ($directors as $director)
            <div data-director-panel="{{ $director['slug'] }}" @class(['hidden' => ! $loop->first])>
                <section class="relative overflow-hidden px-6 pb-24 pt-28 sm:px-10 lg:px-20" style="background: radial-gradient(ellipse at 15% 85%, rgba(54,107,195,0.18) 0%, #0a0a0c 60%);">
                    <div class="pointer-events-none absolute -right-15 -top-15 h-[400px] w-[400px] bg-[radial-gradient(ellipse,rgba(54,107,195,0.13)_0%,transparent_65%)]"></div>
                    <div class="relative z-10 mx-auto max-w-[1800px]" data-reveal>
                        <p class="mb-4 text-xs font-semibold uppercase tracking-[0.26em] text-[#e60012]">{{ $director['eyebrow'] }}</p>
                        <h1 class="max-w-none text-[clamp(3rem,7vw,5.5rem)] font-black uppercase leading-none tracking-[-0.03em] lg:whitespace-nowrap">
                            <span class="block text-white">{{ $director['first_name'] }}</span>
                            <span class="ai-director-name-gradient">{{ $director['last_name'] }}</span>
                        </h1>
                        <p class="mt-7 max-w-5xl text-[clamp(1.35rem,2.9vw,2rem)] font-black uppercase leading-tight tracking-[0.04em] text-white/58">{{ $director['role'] }}</p>
                        <div class="mt-10 flex flex-wrap gap-x-14 gap-y-8 sm:gap-x-20">
                            @foreach ($director['stats'] as $stat)
                                <div>
                                    <div class="text-[clamp(1.6rem,2.8vw,2.3rem)] font-black leading-none tracking-tight">
                                        {{ $stat['value'] }}<span class="text-[#366bc3] {{ $stat['suffix'] === 'yrs' ? 'text-[0.55em] font-bold lowercase' : '' }}">{{ $stat['suffix'] }}</span>
                                    </div>
                                    <div class="mt-3 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/35">{{ $stat['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section class="relative min-h-[560px] overflow-hidden px-6 py-18 sm:px-10 lg:min-h-[640px] lg:px-20 lg:py-24 bg-black">
                    @if (!empty($director['bio_image']))
                        <img src="{{ $director['bio_image'] }}" alt="{{ $director['bio_alt'] }}" class="absolute inset-0 h-full w-full object-cover object-[68%_center]" width="1920" height="1080" loading="lazy">
                    @endif
                    <div class="absolute inset-0 bg-linear-to-r from-[#080809] via-[#080809]/82 to-transparent"></div>
                    <div class="absolute inset-0 bg-linear-to-t from-[#080809]/55 via-transparent to-[#080809]/10"></div>

                    <div class="relative z-10 mx-auto max-w-[1800px]" data-reveal>
                        <div class="max-w-[600px]">
                            <p class="mb-3 text-[10px] font-semibold uppercase tracking-[0.22em] text-[#e60012]">Biography</p>
                            <h2 class="mb-12 text-[clamp(2.4rem,5vw,4.25rem)] font-black uppercase leading-[0.95] tracking-[-0.04em]">
                                <span class="block text-white">{{ $director['bio_title_white'] }}</span>
                                <span class="ai-director-name-gradient">{{ $director['bio_title_gradient'] }}</span>
                            </h2>

                            <div class="space-y-5 text-[13px] font-semibold leading-[1.55] text-white/82 sm:text-sm">
                                @foreach ($director['bio'] as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-[#111111] px-6 py-20 sm:px-10 lg:px-20 lg:py-24">
                    <div class="mx-auto max-w-[1800px]" data-reveal>
                        <p class="mb-4 text-[10px] font-semibold uppercase tracking-[0.22em] text-[#e60012]">{{ $director['works_eyebrow'] }}</p>
                        <h2 class="mb-10 text-[clamp(2.4rem,5vw,4.25rem)] font-black uppercase leading-none tracking-[-0.04em]">
                            <span class="text-white">{{ $director['works_title_white'] }} </span><span class="text-[#4e5d76]">{{ $director['works_title_muted'] }}</span>
                        </h2>

                        @php
                            $worksForLimitCheck = array_slice($director['works'] ?? [], 0, 6);
                            $hasCentered = false;
                            foreach ($worksForLimitCheck as $work) {
                                if (isset($work['span']) && str_contains($work['span'], 'col-start')) {
                                    $hasCentered = true;
                                    break;
                                }
                            }
                            $worksLimit = $hasCentered ? 5 : 6;
                            $displayedWorks = array_slice($director['works'] ?? [], 0, $worksLimit);
                        @endphp

                        <div class="mx-auto max-w-[1280px] bg-[#25292e] p-4 sm:p-5">
                            <div class="grid gap-3 md:grid-cols-6">
                                @foreach ($displayedWorks as $work)
                                    <button
                                        type="button"
                                        class="{{ $work['span'] }} group relative aspect-video overflow-hidden bg-black text-left"
                                        @if (!empty($work['video_url']))
                                            data-video-url="{{ $work['video_url'] }}"
                                            data-video-title="{{ $work['title'] }}"
                                        @else
                                            data-image-url="{{ $work['image'] }}"
                                            data-video-title="{{ $work['title'] }}"
                                        @endif
                                    >
                                        <img src="{{ $work['image'] }}" alt="{{ $work['title'] }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                        <div class="absolute inset-0 bg-black/14 transition group-hover:bg-black/4"></div>
                                        <div class="absolute inset-0 grid place-items-center">
                                            @if (!empty($work['video_url']))
                                                <span class="grid size-10 place-items-center rounded bg-black/72 text-white shadow-lg shadow-black/30 transition group-hover:scale-110" aria-hidden="true">
                                                    <span class="ml-0.5 h-0 w-0 border-y-[7px] border-l-[10px] border-y-transparent border-l-white"></span>
                                                </span>
                                            @else
                                                <span class="grid size-10 place-items-center rounded bg-black/72 text-white shadow-lg shadow-black/30 transition group-hover:scale-110" aria-hidden="true">
                                                    <svg class="size-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        @if (count($director['works'] ?? []) > $worksLimit)
                            <div class="mt-8 text-center">
                                <button type="button" class="inline-flex rounded border border-white/18 px-9 py-3.5 text-[13px] font-bold uppercase tracking-[0.1em] text-white/72 transition hover:border-white/45 hover:bg-white/5 hover:text-white" data-director-work-open="{{ $director['slug'] }}">
                                    View More
                                </button>
                            </div>
                        @endif
                    </div>
                </section>

                <div class="fixed inset-0 z-[100] hidden bg-black/82 px-4 py-6 backdrop-blur-md sm:px-8" data-director-work-modal="{{ $director['slug'] }}" role="dialog" aria-modal="true" aria-labelledby="director-work-title-{{ $director['slug'] }}">
                    <div class="mx-auto flex h-full max-w-[1400px] flex-col overflow-hidden border border-white/10 bg-[#101014] shadow-2xl shadow-black/60">
                        <div class="flex items-center justify-between gap-5 border-b border-white/10 px-5 py-4 sm:px-7">
                            <div>
                                <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.22em] text-[#e60012]">{{ $director['first_name'] }} {{ $director['last_name'] }}</p>
                                <h3 id="director-work-title-{{ $director['slug'] }}" class="text-xl font-black uppercase tracking-[-0.02em] text-white sm:text-2xl">All Works</h3>
                            </div>
                            <button type="button" class="grid size-10 shrink-0 place-items-center rounded border border-white/14 text-xl leading-none text-white/65 transition hover:border-white/35 hover:text-white" data-director-work-close aria-label="Close works gallery">×</button>
                        </div>

                        <div class="min-h-0 flex-1 overflow-y-auto p-5 sm:p-7">
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($director['works'] as $work)
                                    <button
                                        type="button"
                                        class="group relative aspect-video overflow-hidden bg-black text-left"
                                        @if (!empty($work['video_url']))
                                            data-video-url="{{ $work['video_url'] }}"
                                            data-video-title="{{ $work['title'] }}"
                                        @else
                                            data-image-url="{{ $work['image'] }}"
                                            data-video-title="{{ $work['title'] }}"
                                        @endif
                                    >
                                        <img src="{{ $work['image'] }}" alt="{{ $work['title'] }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                        <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/8 to-transparent"></div>
                                        <div class="absolute inset-x-0 bottom-0 p-4">
                                            <p class="text-sm font-black uppercase tracking-[0.04em] text-white">{{ $work['title'] }}</p>
                                        </div>
                                        @if (!empty($work['video_url']))
                                            <span class="absolute left-1/2 top-1/2 grid size-11 -translate-x-1/2 -translate-y-1/2 place-items-center rounded bg-black/72 text-white shadow-lg shadow-black/30 transition group-hover:scale-110" aria-hidden="true">
                                                <span class="ml-0.5 h-0 w-0 border-y-[7px] border-l-[10px] border-y-transparent border-l-white"></span>
                                            </span>
                                        @else
                                            <span class="absolute left-1/2 top-1/2 grid size-11 -translate-x-1/2 -translate-y-1/2 place-items-center rounded bg-black/72 text-white shadow-lg shadow-black/30 transition group-hover:scale-110" aria-hidden="true">
                                                <svg class="size-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Global Premium Video Lightbox Modal -->
    <div class="fixed inset-0 z-[110] hidden bg-black/95 px-4 py-6 backdrop-blur-md sm:px-8" data-director-video-modal role="dialog" aria-modal="true">
        <!-- Close Button -->
        <button type="button" class="absolute top-4 right-4 sm:top-6 sm:right-6 z-50 grid size-10 place-items-center rounded-full border border-white/20 bg-black/72 text-2xl font-light text-white/70 transition hover:border-white/40 hover:bg-black/90 hover:text-white" data-director-video-close aria-label="Close video player">×</button>
        
        <div class="relative mx-auto flex h-full max-w-[1200px] flex-col justify-center">
            
            <!-- Video Wrapper with Aspect Ratio -->
            <div class="relative aspect-video w-full overflow-hidden bg-black shadow-2xl border border-white/10 rounded-lg">
                <!-- HTML5 Video element -->
                <video data-director-video-player class="h-full w-full object-contain hidden" controls autoplay playsinline></video>
                
                <!-- Iframe for YouTube/Vimeo -->
                <iframe data-director-video-iframe class="absolute inset-0 h-full w-full hidden" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>

                <!-- Image element for Visual Stills -->
                <img data-director-video-image class="h-full w-full object-contain hidden" alt="">
            </div>
            
            <!-- Video Title -->
            <div class="mt-4 text-center">
                <h4 data-director-video-title class="text-base font-bold uppercase tracking-wider text-white"></h4>
            </div>
        </div>
    </div>
</main>
