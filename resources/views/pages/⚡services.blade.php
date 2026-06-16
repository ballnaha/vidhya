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
                ['01', 'AI POCs & Previs', 'Rapid proof of concepts, moodboards, and compelling pitch materials to validate your vision before full production begins.', ['Concept validation', 'Moodboard & storyboard', 'Pitch-ready materials', 'Fast turnaround'], '#366bc3'],
                ['02', 'AI Advertising', 'Complete digital campaign creation from the core concept to striking key visuals, delivered in a fraction of traditional timelines.', ['End-to-end campaign creation', 'Key visual development', 'Multi-platform formats', 'Performance-engineered briefs'], '#823665'],
                ['03', 'Post Production', 'Cinematic finishing at scale. We take your hero films and expertly craft high quality variations to feed your entire marketing funnel.', ['Cinematic colour grading', 'Multi-format variations', 'Full funnel asset delivery', 'Broadcast-ready output'], '#e60012'],
                ['04', 'AI Models & Influencers', 'Bespoke persona development and digital character creation for highly targeted brand representation.', ['Custom AI persona creation', 'Brand-aligned characters', 'Scalable digital talent', 'Consistent brand voice'], '#366bc3'],
                ['05', 'AI Marketing Content', 'High volume visual assets engineered for daily engagement across social media and ecommerce platforms.', ['High volume production', 'Social & ecommerce formats', 'Daily engagement assets', 'Brand-consistent output'], '#823665'],
                ['06', 'Micro Drama', 'Highly engaging episodic digital narratives infused with our cinematic perspective and deep storytelling heritage.', ['Episodic narrative series', 'Cinematic storytelling', 'Platform-native formats', 'Audience retention focus'], '#e60012'],
                ['07', 'Training & Workshop', 'Hands on workshops to empower your team to understand and utilize proven AI driven media workflows.', ['Team capability building', 'AI workflow training', 'Hands-on sessions', 'Custom curriculum'], '#366bc3'],
                ['08', 'Strategic Consulting', 'Leveraging our wider ecosystem, we consult on content creation and strategy, ensuring your pipeline drives measurable business results.', ['Full pipeline strategy', 'Ecosystem integration', 'KPI-aligned frameworks', 'Ongoing advisory'], '#823665'],
            ] as [$num, $title, $desc, $bullets, $accent])
                <article class="home-animated-card hover-border-accent grid gap-8 border-l-[3px] bg-[#0c0c12] p-8 hover:bg-[#0f0f18] lg:grid-cols-[100px_1fr_1fr] lg:p-10 lg:px-12" data-reveal style="--card-accent: {{ $accent }}; --reveal-delay: {{ $loop->index * 50 }}ms;">
                    <div>
                        <div class="text-[52px] font-black leading-none text-white/[0.04]">{{ $num }}</div>
                        <div class="mt-3 h-0.5 w-6" style="background: {{ $accent }}"></div>
                    </div>
                    <div>
                        <h2 class="mb-4 text-lg font-black uppercase tracking-[0.04em]">{{ $title }}</h2>
                        <p class="text-sm font-normal leading-[1.85] text-white/48">{{ $desc }}</p>
                    </div>
                    <div>
                        @foreach ($bullets as $bullet)
                            <div class="mb-2.5 flex items-center gap-3 text-[13px] font-normal text-white/50">
                                <span class="size-[5px] shrink-0 rounded-full" style="background: {{ $accent }}"></span>
                                <span>{{ $bullet }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-20 text-center" data-reveal>
            <a href="{{ route('contact') }}" class="inline-flex rounded px-14 py-4 text-sm font-bold uppercase tracking-[0.1em] transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" wire:navigate.hover>Start a Project</a>
        </div>
    </section>
</main>
