<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('About')]
#[Layout('layouts.marketing')]
class extends Component
{
    //
}; ?>

<main class="bg-[#0a0a0c] text-white">
    <section class="relative isolate overflow-hidden px-6 pb-24 pt-40 sm:px-10 lg:px-20">
        <img
            src="/images/bg_about.webp"
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
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.26em] text-white/35">Our DNA</p>
            <h1 class="max-w-none text-[clamp(3rem,6.4vw,5.35rem)] font-black uppercase leading-none tracking-[-0.03em] lg:whitespace-nowrap">
                <span class="bg-linear-to-r from-[#366bc3] via-[#6d55a5] to-[#823665] bg-clip-text text-transparent">Filmmaking In </span><span class="bg-linear-to-r from-[#823665] via-[#b4143c] to-[#e60012] bg-clip-text text-transparent">Our DNA.</span>
            </h1>
            <p class="mt-6 max-w-2xl text-[17px] font-normal leading-[1.8] text-white/48">Technology without taste is useless, but expertise with standard is timeless. As the new venture from Benetone Films, we apply two decades of cinematic excellence to the next frontier of media.</p>
        </div>
    </section>

    <section class="px-6 py-24 sm:px-10 lg:px-20">
        <div class="mx-auto grid max-w-[1800px] gap-16 lg:grid-cols-2 lg:gap-24">
            <div data-reveal="left">
                <p class="mb-4 text-xs font-semibold uppercase tracking-[0.26em] text-white/35">Who We Are</p>
                <h2 class="mb-8 text-[clamp(2rem,3.5vw,3rem)] font-black uppercase leading-tight tracking-[-0.02em]">Human Direction.<br><span class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-transparent">AI Amplified.</span></h2>
                <p class="mb-5 text-[15px] font-normal leading-[1.85] text-white/50">In a world of fast moving trends, we combine human creative direction with powerful AI tools to produce premium visual content. We bring a deeply knowledgeable team and true artisanal craft directly into AI video creation.</p>
                <p class="text-[15px] font-normal leading-[1.85] text-white/38">While our core is world class video creation, we design every asset with future success in mind, setting the perfect foundation to feed your marketing funnel and expand your audience reach.</p>
            </div>

            <div class="space-y-1">
                @foreach ([
                    ['01', 'The Cinematic Perspective', 'We apply a traditional director\'s eye to every frame. Our heritage ensures that even the fastest digital assets possess the depth, lighting, and narrative soul of premium film and television.', '#366bc3'],
                    ['02', 'Defined Workflows & Faster Output', 'Creativity should not be restricted by logistical bottlenecks. We rely on highly structured, predictable pipelines to guarantee consistent, premium results delivered in significantly less time.', '#823665'],
                    ['03', 'Ethical Craft & Strict Security', 'Innovation must never compromise your intellectual property. We maintain rigorous data security protocols and commit to ethical AI practices.', '#e60012'],
                    ['04', 'The Benetone Ecosystem', 'Working alongside Benetone Advertising and Benetone Originals, we can build specific digital campaigns from scratch or scale a single hero film into hundreds of assets.', '#366bc3'],
                ] as [$num, $title, $desc, $accent])
                    <article class="home-animated-card border-l-[3px] bg-[#0d0d13] p-7 hover:bg-[#101018]" data-reveal="right" style="border-color: {{ $accent }}; --reveal-delay: {{ $loop->index * 80 }}ms;">
                        <div class="flex gap-4">
                            <span class="pt-1 text-[0.65rem] font-black tracking-[0.08em] text-white/15">{{ $num }}</span>
                            <div>
                                <h3 class="mb-2 text-sm font-black uppercase tracking-[0.04em]">{{ $title }}</h3>
                                <p class="text-[13px] font-normal leading-[1.75] text-white/42">{{ $desc }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="border-t border-white/5 bg-[#080809] px-6 py-24 sm:px-10 lg:px-20">
        <div class="mx-auto max-w-[1800px]">
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.26em] text-white/35">The Vidhya Advantage</p>
            <div class="mb-16 grid gap-12 lg:grid-cols-2 lg:items-end" data-reveal>
                <h2 class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-[clamp(2.2rem,4.5vw,3.75rem)] font-black uppercase leading-none tracking-[-0.02em] text-transparent">Our Philosophy.</h2>
                <p class="text-[15px] font-normal leading-[1.85] text-white/45">At Vidhya Studio, artificial intelligence is an invitation to scale your vision. We blend the precision of advanced technology with the soul of human storytelling, empowering brands to move with unmatched speed.</p>
            </div>

            <div class="grid gap-[3px] md:grid-cols-2">
                @foreach ([
                    ['01', 'Knowledgeable Human Craft', 'AI might be the most advanced tool in the room, but our knowledgeable team remains at the core.', '#366bc3'],
                    ['02', 'Defined Workflows & Faster Output', 'Our defined workflows allow us to move from concept to execution at record speed.', '#823665'],
                    ['03', 'The Cinematic Perspective', 'Traditional craftsmanship and advanced technology coexist harmoniously across a flexible technology stack.', '#e60012'],
                    ['04', 'Creativity With Accountability', 'Every piece of visual content we produce is engineered to capture attention and feed your marketing funnel.', '#366bc3'],
                ] as [$num, $title, $desc, $accent])
                    <article class="border-t-2 bg-[#0d0d12] p-9" data-reveal style="border-color: {{ $accent }}; --reveal-delay: {{ $loop->index * 70 }}ms;">
                        <div class="flex gap-5">
                            <span class="pt-1 text-xs font-black tracking-[0.08em] text-white/15">{{ $num }}</span>
                            <div>
                                <h3 class="mb-3 text-sm font-black uppercase tracking-[0.04em]">{{ $title }}</h3>
                                <p class="text-[13px] font-normal leading-[1.85] text-white/45">{{ $desc }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="px-6 py-20 text-center sm:px-10 lg:px-20">
        <div data-reveal>
            <a href="{{ route('contact') }}" class="inline-flex rounded px-14 py-4 text-sm font-bold uppercase tracking-[0.1em] transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" wire:navigate.hover>Start a Conversation</a>
        </div>
    </section>
</main>
