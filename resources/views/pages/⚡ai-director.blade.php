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
            'directors' => Director::all(),
        ]);
    }
};
?>

<main class="bg-[#0a0a0c] text-white" data-director-tabs>
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

                    <div class="mx-auto max-w-[1280px] bg-[#25292e] p-4 sm:p-5">
                        <div class="grid gap-3 md:grid-cols-6">
                            @foreach (array_slice($director['works'], 0, 5) as $work)
                                <button
                                    type="button"
                                    class="{{ $work['span'] }} group relative aspect-video overflow-hidden bg-black text-left"
                                    @if (!empty($work['video_url']))
                                        data-video-url="{{ $work['video_url'] }}"
                                        data-video-title="{{ $work['title'] }}"
                                    @else
                                        data-director-work-open="{{ $director['slug'] }}"
                                    @endif
                                >
                                    <img src="{{ $work['image'] }}" alt="{{ $work['title'] }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                    <div class="absolute inset-0 bg-black/14 transition group-hover:bg-black/4"></div>
                                    <div class="absolute inset-0 grid place-items-center">
                                        <span class="grid size-10 place-items-center rounded bg-black/72 text-white shadow-lg shadow-black/30 transition group-hover:scale-110" aria-hidden="true">
                                            <span class="ml-0.5 h-0 w-0 border-y-[7px] border-l-[10px] border-y-transparent border-l-white"></span>
                                        </span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if (count($director['works']) > 5)
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
                                    @endif
                                >
                                    <img src="{{ $work['image'] }}" alt="{{ $work['title'] }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                    <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/8 to-transparent"></div>
                                    <div class="absolute inset-x-0 bottom-0 p-4">
                                        <p class="text-sm font-black uppercase tracking-[0.04em] text-white">{{ $work['title'] }}</p>
                                    </div>
                                    <span class="absolute left-1/2 top-1/2 grid size-11 -translate-x-1/2 -translate-y-1/2 place-items-center rounded bg-black/72 text-white shadow-lg shadow-black/30 transition group-hover:scale-110" aria-hidden="true">
                                        <span class="ml-0.5 h-0 w-0 border-y-[7px] border-l-[10px] border-y-transparent border-l-white"></span>
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

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
            </div>
            
            <!-- Video Title -->
            <div class="mt-4 text-center">
                <h4 data-director-video-title class="text-base font-bold uppercase tracking-wider text-white"></h4>
            </div>
        </div>
    </div>
</main>
