<?php

use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Title('Home')]
#[Layout('layouts.marketing')]
class extends Component
{
    //
}; ?>

@php
    use App\Models\SiteSetting;

    $heroVideoPath = SiteSetting::homeHeroVideoPath();
    $heroPosterPath = SiteSetting::homeHeroPosterPath();
@endphp

<main class="relative overflow-hidden bg-[#0a0a0c] text-white">
    <section class="relative min-h-screen overflow-hidden bg-[#0a0a0c] px-6 pb-12 pt-36 sm:px-10 lg:px-20">
        <video
            class="home-hero-youtube-video pointer-events-none absolute left-1/2 top-0 max-w-none -translate-x-1/2 object-cover object-center border-0"
            autoplay
            muted
            loop
            playsinline
            preload="auto"
            poster="{{ $heroPosterPath }}"
            aria-hidden="true"
            tabindex="-1"
        >
            <source src="{{ $heroVideoPath }}" type="video/mp4">
        </video>
        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(90deg,rgba(5,7,12,0.82)_0%,rgba(5,7,12,0.68)_38%,rgba(5,7,12,0.30)_70%,rgba(5,7,12,0.16)_100%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(180deg,rgba(5,7,12,0.28)_0%,rgba(5,7,12,0.06)_42%,rgba(0,0,0,0.62)_82%,#000_100%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_20%_80%,rgba(54,107,195,0.22)_0%,transparent_58%)]"></div>
        <div class="pointer-events-none absolute right-[-5%] top-[5%] h-[600px] w-[600px] bg-[radial-gradient(ellipse,rgba(230,0,18,0.12)_0%,transparent_65%)]"></div>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-[42vh] min-h-80 bg-[linear-gradient(180deg,transparent_0%,rgba(8,8,9,0.58)_26%,rgba(8,8,9,0.94)_58%,#080809_82%,#080809_100%)]"></div>
        <div class="pointer-events-none absolute right-[-10px] top-1/2 hidden -translate-y-1/2 select-none text-[clamp(200px,28vw,400px)] font-black leading-none tracking-[-0.05em] text-white/[0.022] lg:block"></div>
        <div class="relative z-10 mx-auto max-w-[1800px]">
            <div class="max-w-[1100px]">
                <div class="mb-7 flex items-center gap-4" data-hero-reveal style="--hero-delay: 100ms; --hero-duration: 700ms; --hero-y: 20px;">
                    <span class="h-[3px] w-10 bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012]"></span>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-white/60">{{ __('Your AI strategic content partner') }}</p>
                </div>

                <h1 class="text-[clamp(3rem,6.5vw,5.7rem)] font-black uppercase leading-[1.05] tracking-[-0.03em]" data-hero-reveal style="--hero-delay: 250ms; --hero-duration: 850ms; --hero-y: 30px;">
                    <span class="block bg-linear-to-r from-[#366bc3] via-[#5961b9] to-[#823665] bg-clip-text text-transparent">{{ __('Cinematic') }}</span>
                    <span class="block bg-linear-to-r from-[#823665] via-[#b4143c] to-[#e60012] bg-clip-text text-transparent">{{ __('Perspective.') }}</span>
                    <span class="block text-white">{{ __('AI Speed.') }}</span>
                    <span class="block bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-transparent">{{ __('Measurable Impact.') }}</span>
                </h1>

                <p class="mt-9 max-w-[680px] text-[17px] font-medium leading-[1.8] text-white/50" data-hero-reveal style="--hero-delay: 450ms; --hero-duration: 800ms; --hero-y: 20px;">
                    {{ __('We are a creative AI studio that directs, not just generates. Backed by two decades of cinematic excellence, our knowledgeable team builds everything from high volume advertising to premium AI hybrids and micro dramas — delivering superior visual output in a fraction of the time.') }}
                </p>

                <div class="mt-[52px] flex flex-col gap-4 sm:flex-row" data-hero-reveal style="--hero-delay: 600ms; --hero-duration: 800ms; --hero-y: 20px;">
                    <a href="{{ route('contact') }}" class="inline-flex min-h-12 items-center justify-center rounded px-9 text-[13px] font-bold uppercase tracking-[0.1em] text-white shadow-[0_14px_34px_rgba(230,0,18,0.28)] transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" wire:navigate.hover>
                        {{ __('Start a project') }}
                    </a>
                    <a href="{{ route('services') }}" class="inline-flex min-h-12 items-center justify-center rounded border border-white/22 px-9 text-[13px] font-semibold uppercase tracking-[0.08em] text-white transition hover:border-white/50 hover:bg-white/5" wire:navigate.hover>
                        {{ __('Our services') }}
                    </a>
                </div>
            </div>

            <div class="mt-20 grid border-t border-white/8 bg-white/[0.035] backdrop-blur-sm md:grid-cols-3" data-hero-reveal="cards" style="--hero-delay: 900ms; --hero-duration: 1000ms;">
                @foreach ([
                    ['bar' => 'from-[#3f72d8] to-[#3f72d8]', 'title' => 'The cinematic perspective', 'body' => 'Producing everything from rapid digital assets to complex AI hybrid films with a premium visual standard.'],
                    ['bar' => 'from-[#8b3f92] to-[#7a1f3c]', 'title' => 'Superior output faster', 'body' => 'A structured pipeline that ensures higher quality deliverables in significantly less time.'],
                    ['bar' => 'from-[#ff0014] to-[#e60012]', 'title' => 'Built for performance', 'body' => 'Content engineered with strategy in mind, ensuring your assets are ready to drive results as your marketing scales.'],
                ] as $feature)
                    <article class="relative min-h-32 border-b border-r border-black/40 px-7 py-7 last:border-r-0 md:border-b-0">
                        <span class="absolute inset-x-0 top-0 h-0.5 bg-linear-to-r {{ $feature['bar'] }}"></span>
                        <h2 class="text-[13px] font-black uppercase tracking-[0.02em] text-white">{{ __($feature['title']) }}</h2>
                        <p class="mt-2.5 max-w-xl text-[13px] font-medium leading-[1.75] text-white/60">{{ __($feature['body']) }}</p>
                    </article>
                @endforeach
            </div>

            <div class="hero-scroll-cue mt-5 flex flex-col items-center gap-2 text-[10px] font-normal uppercase tracking-[0.22em] text-white/60" data-hero-reveal style="--hero-delay: 1300ms; --hero-duration: 1000ms; --hero-y: 12px;">
                <span class="hero-scroll-cue__label">{{ __('Scroll') }}</span>
                <span class="hero-scroll-cue__line"></span>
            </div>
        </div>
    </section>

    <section class="marketing-deferred-section relative overflow-hidden border-t border-white/5 bg-[#080809] px-6 py-28 sm:px-10 lg:px-20">
        <img src="/images/previs1.webp" alt="" class="pointer-events-none absolute inset-0 h-full w-full object-cover object-center" loading="lazy" decoding="async" aria-hidden="true">
        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(90deg,rgba(8,8,9,0.72)_0%,rgba(8,8,9,0.50)_48%,rgba(8,8,9,0.30)_100%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_18%_38%,rgba(54,107,195,0.18)_0%,transparent_46%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_88%_28%,rgba(230,0,18,0.10)_0%,transparent_38%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(180deg,transparent_38%,rgba(8,8,9,0.45)_72%,#080809_100%)]"></div>

        <div class="relative mx-auto grid max-w-[1800px] gap-16 lg:grid-cols-2 lg:gap-24">
            <div data-reveal="left">
                <p class="mb-4 text-[11px] font-semibold uppercase tracking-[0.26em] text-white/60">Our DNA</p>
                <h2 class="mb-8 text-[clamp(2.5rem,5vw,4rem)] font-black uppercase leading-none tracking-[-0.02em]">
                    <span>Filmmaking</span><br><span class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-transparent">In Our DNA.</span>
                </h2>
                <p class="mb-5 max-w-xl text-[15px] font-normal leading-[1.85] text-white/50">Technology without taste is useless, but expertise with standard is timeless. As the new venture from Benetone Films, we apply two decades of cinematic excellence to the next frontier of media.</p>
                <p class="mb-10 max-w-xl text-[15px] font-normal leading-[1.85] text-white/38">In a world of fast moving trends, we combine human creative direction with powerful AI tools to produce premium visual content.</p>
                <a href="{{ route('about') }}" class="inline-flex rounded border border-white/22 px-9 py-3.5 text-[13px] font-semibold uppercase tracking-[0.08em] transition hover:border-white/50" wire:navigate.hover>Learn More About Us</a>
            </div>

            <div class="space-y-1">
                @foreach ([
                    ['The Cinematic Perspective', 'We apply a traditional director\'s eye to every frame. Our heritage ensures that even the fastest digital assets possess the depth, lighting, and narrative soul of premium film and television.', '#366bc3'],
                    ['Defined Workflows & Faster Output', 'Creativity should not be restricted by logistical bottlenecks. We rely on highly structured, predictable pipelines to guarantee consistent, premium results.', '#823665'],
                    ['Ethical Craft & Strict Security', 'We maintain rigorous data security protocols and commit to ethical AI practices, ensuring our creativity remains purposeful, responsible, and impactful.', '#e60012'],
                    ['The Benetone Ecosystem', 'Working alongside Benetone Advertising and Benetone Originals, we can build campaigns from scratch or scale a hero film into hundreds of assets.', '#366bc3'],
                ] as [$title, $desc, $accent])
                    <article class="home-animated-card border-l-[3px] bg-[#0d0d13] p-7 hover:bg-[#101018]" data-reveal="right" style="border-color: {{ $accent }}; --reveal-delay: {{ $loop->index * 80 }}ms;">
                        <h3 class="mb-2.5 text-sm font-black uppercase tracking-[0.04em]">{{ $title }}</h3>
                        <p class="text-[13px] font-normal leading-[1.8] text-white/45">{{ $desc }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="marketing-deferred-section border-t border-white/5 bg-[#0a0a0c] px-6 pb-28 sm:px-10 lg:px-20">
        <div class="mx-auto max-w-[1800px]">
            <div class="relative left-1/2 w-screen -translate-x-1/2 overflow-hidden py-28">
                <img src="/images/home3.webp" alt="" class="pointer-events-none absolute inset-0 h-full w-full object-cover object-center" loading="lazy" decoding="async" aria-hidden="true">
                <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,7,0.92)_0%,rgba(5,5,7,0.72)_52%,rgba(5,5,7,0.52)_100%)]"></div>

                <div class="relative mx-auto max-w-[1800px] px-6 sm:px-10 lg:px-20" data-reveal>
                    <p class="mb-4 text-[11px] font-semibold uppercase tracking-[0.26em] text-white/60">The Vidhya Advantage</p>
                    <div class="grid gap-10 lg:grid-cols-2 lg:items-end">
                        <h2 class="text-[clamp(2.5rem,5vw,4rem)] font-black uppercase leading-none tracking-[-0.02em]">
                            <span class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-transparent">Our Philosophy.</span>
                        </h2>
                        <p class="max-w-2xl text-[15px] font-normal leading-[1.85] text-white/55">
                            At Vidhya Studio, artificial intelligence is an invitation to scale your vision. We blend the precision of advanced technology with the soul of human storytelling — empowering brands to move with unmatched speed without ever compromising the depth of their message.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid gap-[3px] md:grid-cols-2">
                @foreach ([
                    ['num' => '01', 'title' => 'Knowledgeable Human Craft', 'body' => 'AI might be the most advanced tool in the room, but our knowledgeable team remains at the core. We use technology as an extension of our directors, ensuring the final work feels deeply human.', 'accent' => '#366bc3'],
                    ['num' => '02', 'title' => 'Defined Workflows & Faster Output', 'body' => 'Creativity should not be restricted by logistical bottlenecks. Our defined workflows allow us to move from concept to execution at record speed, delivering superior visual assets in significantly less time.', 'accent' => '#823665'],
                    ['num' => '03', 'title' => 'The Cinematic Perspective', 'body' => 'It is not a battle between traditional production and AI. We believe traditional craftsmanship and advanced technology coexist harmoniously — applied across a flexible technology stack to match your exact aesthetic.', 'accent' => '#e60012'],
                    ['num' => '04', 'title' => 'Creativity With Accountability', 'body' => 'Boundless imagination means nothing without measurable impact. Every piece of visual content we produce is engineered to capture attention, reach wider audiences, and set the perfect foundation to feed your marketing funnel.', 'accent' => '#366bc3'],
                ] as $belief)
                    <article class="border-t-2 bg-[#0d0d12] px-8 py-9 sm:px-10 sm:py-10" data-reveal style="border-color: {{ $belief['accent'] }}; --reveal-delay: {{ $loop->index * 80 }}ms;">
                        <div class="flex items-start gap-5">
                            <span class="shrink-0 pt-1 text-[11px] font-black tracking-[0.08em] text-white/15">{{ $belief['num'] }}</span>
                            <div>
                                <h3 class="mb-3 text-base font-black uppercase tracking-[0.04em] text-white">{{ $belief['title'] }}</h3>
                                <p class="text-[13px] font-normal leading-[1.85] text-white/45">{{ $belief['body'] }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="marketing-deferred-section relative overflow-hidden border-t border-white/5 bg-[#080809] px-6 pb-28 sm:px-10 lg:px-20">
        <div class="pointer-events-none absolute bottom-[-80px] right-[-80px] h-[500px] w-[500px] bg-[radial-gradient(ellipse,rgba(230,0,18,0.1)_0%,transparent_65%)]"></div>
        <div class="relative z-10 mx-auto max-w-[1800px]">
            <div class="relative left-1/2 w-screen -translate-x-1/2 overflow-hidden py-28">
                <img src="/images/microdrama1.webp" alt="" class="pointer-events-none absolute inset-0 h-full w-full object-cover object-center" loading="lazy" decoding="async" aria-hidden="true">
                <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,7,0.78)_0%,rgba(5,5,7,0.55)_52%,rgba(5,5,7,0.35)_80%)]"></div>

                <div class="relative mx-auto max-w-[1800px] px-6 sm:px-10 lg:px-20" data-reveal>
                    <p class="mb-4 text-[11px] font-semibold uppercase tracking-[0.26em] text-white/60">How We Build With You</p>
                    <div class="grid gap-12 lg:grid-cols-2 lg:items-end">
                        <h2 class="text-[clamp(2.5rem,5vw,3.75rem)] font-black uppercase leading-[1.05] tracking-[-0.02em]">
                            <span class="text-white">Strategy, Scale</span><br>
                            <span class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-transparent">&amp; Cinematic <br> Depth.</span>
                        </h2>
                        <p class="max-w-2xl text-[15px] font-normal leading-[1.85] text-white/55">
                            Vidhya Studio operates at the highest level of global entertainment and advertising. We start by deeply understanding your core message — whether you are a direct brand, a CMO, or a creative partner.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-16 grid gap-[3px] lg:grid-cols-3">
                @foreach ([
                    ['title' => 'Strategic Visual Development', 'body' => 'We take the time to understand your core message. By combining human creative direction with powerful AI tools, we build bespoke models tailored exactly to your specific vision and performance goals.', 'accent' => '#366bc3'],
                    ['title' => 'Long Format & Narrative Depth', 'body' => 'We go far beyond rapid social media clips. Applying our cinematic perspective, we develop and execute long format content including premium series assets, standalone AI films, and episodic micro dramas.', 'accent' => '#823665'],
                    ['title' => 'Seamless Campaign Scaling', 'body' => 'If you need massive digital volume, we can take a single hero film and scale it into hundreds of targeted marketing assets — feeding your entire marketing funnel with high quality variations at record speed.', 'accent' => '#e60012'],
                ] as $model)
                    <article class="border-t-2 bg-[#0d0d13] px-8 py-9" data-reveal style="border-color: {{ $model['accent'] }}; --reveal-delay: {{ $loop->index * 90 }}ms;">
                        <div class="mb-6 flex h-9 w-9 items-center justify-center rounded border" style="background: {{ $model['accent'] }}22; border-color: {{ $model['accent'] }}44;">
                            <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $model['accent'] }}"></span>
                        </div>
                        <h3 class="mb-3.5 text-[15px] font-black uppercase tracking-[0.04em] text-white">{{ $model['title'] }}</h3>
                        <p class="text-[13px] font-normal leading-[1.85] text-white/45">{{ $model['body'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="flex flex-col gap-8 border-l-[3px] border-white/8 bg-[#0d0d13] px-8 py-9 lg:flex-row lg:items-center lg:justify-between lg:px-12" data-reveal>
                <div>
                    <h3 class="mb-2 text-base font-black uppercase tracking-[0.04em] text-white">Who We Serve</h3>
                    <p class="max-w-[680px] text-sm font-normal leading-[1.8] text-white/45">Our services are designed for performance led marketers, growing businesses, and creative agencies who need a smarter way to produce compelling, on-brand content at an unprecedented scale.</p>
                </div>
                <a href="{{ route('contact') }}" class="inline-flex shrink-0 rounded px-9 py-3.5 text-[13px] font-bold uppercase tracking-[0.1em] transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" wire:navigate.hover>Start a Project</a>
            </div>
        </div>
    </section>

    <section class="marketing-deferred-section relative overflow-hidden bg-[#0a0a0c] px-6 py-28 text-center sm:px-10 lg:px-20">
        <img src="/images/home5.webp" alt="" class="pointer-events-none absolute inset-0 h-full w-full object-cover object-center" loading="lazy" decoding="async" aria-hidden="true">
        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,7,0.80)_0%,rgba(5,5,7,0.72)_50%,rgba(5,5,7,0.80)_100%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(54,107,195,0.14)_0%,transparent_65%)]"></div>
        <div class="relative z-10" data-reveal>
            <p class="mb-4 text-[11px] font-semibold uppercase tracking-[0.26em] text-white/60">Ready to Scale?</p>
            <h2 class="mb-6 text-[clamp(2.8rem,6vw,5rem)] font-black uppercase leading-[1.05] tracking-[-0.03em]">
                <span class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-transparent">Ready to Scale</span><br>
                <span>Your Vision?</span>
            </h2>
            <p class="mx-auto mb-12 max-w-xl text-[17px] font-normal leading-[1.8] text-white/48">It all starts with a conversation. Let us explore how the Vidhya Studio engine can support your creative strategy and drive measurable results.</p>
            <a href="{{ route('contact') }}" class="inline-flex rounded px-14 py-4 text-sm font-bold uppercase tracking-[0.1em] transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" wire:navigate.hover>Start a Project</a>
        </div>
    </section>
</main>
