<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Service;

new #[Title('Services')]
#[Layout('layouts.marketing')]
class extends Component
{
    public function with(): array
    {
        return [
            'services' => Service::query()->orderBy('sort_order')->get(),
        ];
    }
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
            @foreach ($services as $service)
                <article class="home-animated-card group hover-border-accent flex flex-row items-stretch overflow-hidden border-l-[3px] bg-[#0c0c12] hover:bg-[#0f0f18]" data-reveal style="--card-accent: {{ $service->accent }}; --reveal-delay: {{ $loop->index * 50 }}ms;">
                    <div class="relative shrink-0 w-28 sm:w-40 lg:w-[200px] xl:w-[240px] bg-black aspect-[3/4]" style="aspect-ratio: 3/4;">
                        @if ($service->image)
                            <img src="{{ $service->image }}" alt="{{ $service->title }}" class="h-full w-full object-cover object-center transition duration-500 hover:scale-105" loading="lazy">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center overflow-hidden bg-[#050507]" role="img" aria-label="{{ __('Service number :number', ['number' => $service->num]) }}">
                                <span class="relative z-10 text-4xl font-black tracking-[-0.06em] text-white/10 sm:text-5xl lg:text-6xl" style="text-shadow: 0 0 40px {{ $service->accent }}25;">
                                    {{ $service->num }}
                                </span>
                            </div>
                        @endif
                        <!-- Fade overlay to blend image into the card background -->
                        <div class="absolute inset-y-0 right-0 w-3/5 transition-colors duration-250" style="background: linear-gradient(90deg, transparent 0%, rgba(12, 12, 18, 1) 100%);"></div>
                        <!-- Subtle bottom overlay for contrast -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/15 via-transparent to-transparent"></div>
                        <!-- Number badge -->
                        @if ($service->image)
                            <div class="absolute top-3 left-3 rounded bg-black/70 backdrop-blur-md px-2 py-0.5 text-[9px] font-black tracking-wider text-white border border-white/10" style="border-color: {{ $service->accent }}40">
                                {{ $service->num }}
                            </div>
                        @endif
                    </div>
                    <div class="grid flex-1 gap-6 lg:grid-cols-2 lg:gap-8 p-6 sm:p-8 lg:p-10 lg:px-12 items-center">
                        <div>
                            <h2 class="mb-3 text-lg font-black uppercase tracking-[0.04em] sm:text-xl">
                                <a href="{{ route('portfolio', ['service' => $service->id]) }}" class="hover:text-white/80 transition" wire:navigate>
                                    {{ $service->title }}
                                </a>
                            </h2>
                            <p class="text-sm font-normal leading-[1.8] text-white/60 sm:text-base mb-4">{{ $service->description }}</p>
                            <a href="{{ route('portfolio', ['service' => $service->id]) }}" class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-wider transition hover:brightness-110" style="color: {{ $service->accent }}" wire:navigate>
                                <span>{{ __('View Portfolio Outputs') }}</span>
                                <svg class="size-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                        <div class="flex flex-col justify-center gap-2.5">
                            @foreach ($service->bullets as $bullet)
                                <div class="flex items-center gap-3 text-sm font-normal text-white/70">
                                    <span class="size-[6px] shrink-0 rounded-full" style="background: {{ $service->accent }}"></span>
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
