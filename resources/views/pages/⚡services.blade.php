<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Services')]
#[Layout('layouts.marketing')]
class extends Component
{
    //
}; ?>

<main class="bg-[#0a0a0c] text-white">
    <section class="relative overflow-hidden px-6 pb-24 pt-40 sm:px-10 lg:px-20" style="background: radial-gradient(ellipse at 15% 85%, rgba(54,107,195,0.18) 0%, #0a0a0c 60%);">
        <div class="relative z-10 mx-auto max-w-[1800px]" data-reveal>
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.26em] text-white/35">What We Build</p>
            <h1 class="max-w-5xl text-[clamp(3rem,7vw,5.5rem)] font-black uppercase leading-none tracking-[-0.03em]">
                <span class="bg-linear-to-r from-[#366bc3] via-[#6d55a5] to-[#823665] bg-clip-text text-transparent">Our </span><span class="bg-linear-to-r from-[#823665] via-[#b4143c] to-[#e60012] bg-clip-text text-transparent">Services</span>
            </h1>
            <p class="mt-6 max-w-2xl text-[17px] font-normal leading-[1.8] text-white/48">From the initial spark of an idea to massive global delivery. We blend deep filmmaking expertise with advanced technology and a well defined workflow to produce superior content tailored to your exact needs.</p>
        </div>
    </section>

    <section class="px-6 py-20 sm:px-10 lg:px-20">
        <div class="mx-auto max-w-[1800px] space-y-[3px]">
            @foreach ([
                ['01', 'AI POCs & Previs', 'Rapid proof of concepts, moodboards, and compelling pitch materials to validate your vision before full production begins.', ['Concept validation', 'Moodboard & storyboard', 'Pitch-ready materials', 'Fast turnaround'], '#366bc3', '/images/services/previs.webp'],
                ['02', 'AI Advertising', 'Complete digital campaign creation from the core concept to striking key visuals, delivered in a fraction of traditional timelines.', ['End-to-end campaign creation', 'Key visual development', 'Multi-platform formats', 'Performance-engineered briefs'], '#823665', '/images/services/ai_adver.webp'],
                ['03', 'AI Post Production', 'Cinematic finishing at scale. We take your hero films and expertly craft high quality variations to feed your entire marketing funnel.', ['Cinematic colour grading', 'Multi-format variations', 'Full funnel asset delivery', 'Broadcast-ready output'], '#e60012', '/images/services/post.webp'],
                ['04', 'AI Models & Influencers', 'Bespoke persona development and digital character creation for highly targeted brand representation.', ['Custom AI persona creation', 'Brand-aligned characters', 'Scalable digital talent', 'Consistent brand voice'], '#366bc3', '/images/services/content.webp'],
                ['05', 'Micro Drama', 'Highly engaging episodic digital narratives infused with our cinematic perspective and deep storytelling heritage.', ['Episodic narrative series', 'Cinematic storytelling', 'Platform-native formats', 'Audience retention focus'], '#e60012', '/images/services/microdrama.webp'],
            ] as [$num, $title, $desc, $bullets, $accent, $img])
                <article class="home-animated-card group hover-border-accent flex flex-row items-stretch overflow-hidden border-l-[3px] bg-[#0c0c12] hover:bg-[#0f0f18]" data-reveal style="--card-accent: {{ $accent }}; --reveal-delay: {{ $loop->index * 50 }}ms;">
                    <div class="relative shrink-0 w-28 sm:w-40 lg:w-[200px] xl:w-[240px] bg-black aspect-[3/4]" style="aspect-ratio: 3/4;">
                        <img src="{{ $img }}" alt="{{ $title }}" class="h-full w-full object-cover object-center transition duration-500 hover:scale-105" loading="lazy">
                        <!-- Fade overlay to blend image into the card background -->
                        <div class="absolute inset-y-0 right-0 w-3/5 transition-colors duration-250" style="background: linear-gradient(90deg, transparent 0%, rgba(12, 12, 18, 1) 100%);"></div>
                        <!-- Subtle bottom overlay for contrast -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/15 via-transparent to-transparent"></div>
                        <!-- Number badge -->
                        <div class="absolute top-3 left-3 rounded bg-black/70 backdrop-blur-md px-2 py-0.5 text-[9px] font-black tracking-wider text-white border border-white/10" style="border-color: {{ $accent }}40">
                            {{ $num }}
                        </div>
                    </div>
                    <div class="grid flex-1 gap-6 lg:grid-cols-2 lg:gap-8 p-6 sm:p-8 lg:p-10 lg:px-12 items-center">
                        <div>
                            <h2 class="mb-3 text-lg font-black uppercase tracking-[0.04em] sm:text-xl">{{ $title }}</h2>
                            <p class="text-sm font-normal leading-[1.8] text-white/60 sm:text-base">{{ $desc }}</p>
                        </div>
                        <div class="flex flex-col justify-center gap-2.5">
                            @foreach ($bullets as $bullet)
                                <div class="flex items-center gap-3 text-sm font-normal text-white/70">
                                    <span class="size-[6px] shrink-0 rounded-full" style="background: {{ $accent }}"></span>
                                    <span>{{ $bullet }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-20 text-center" data-reveal>
            <a href="{{ route('contact') }}" class="inline-flex rounded px-14 py-4 text-sm font-bold uppercase tracking-[0.1em] transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" wire:navigate.hover>Start a Project</a>
        </div>
    </section>
</main>
