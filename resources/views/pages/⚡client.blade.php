<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Client as ClientModel;

new #[Title('Client')]
#[Layout('layouts.marketing')]
class extends Component
{
    public function render()
    {
        return view('pages.⚡client', [
            'clients' => ClientModel::query()->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }
};
?>

<main class="bg-[#0a0a0c] text-white">
    <section class="relative isolate overflow-hidden px-6 pb-24 pt-40 sm:px-10 lg:px-20">
        <img
            src="/images/bg_faq.webp"
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
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.26em] text-white/35">Our Clients</p>
            <h1 class="max-w-none text-[clamp(3rem,6.4vw,5.35rem)] font-black uppercase leading-none tracking-[-0.03em]">
                <span class="bg-linear-to-r from-[#366bc3] via-[#6d55a5] to-[#823665] bg-clip-text text-transparent">Trusted By </span><span class="bg-linear-to-r from-[#823665] via-[#b4143c] to-[#e60012] bg-clip-text text-transparent">Leading Brands</span>
            </h1>
            <p class="mt-6 max-w-3xl text-[17px] font-normal leading-[1.8] text-white/48">From concept to production, we help brands bring ideas to life with AI-enhanced creativity and cinematic production.</p>
        </div>
    </section>

    <section class="px-6 py-20 sm:px-10 sm:py-24 lg:px-20">
        <div class="mx-auto max-w-[1800px]" data-client-carousel-shell>
            <div class="flex items-end justify-between gap-6" data-reveal>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#366bc3]">Featured Clients</p>
                    <h2 class="mt-4 text-[clamp(2rem,4vw,3.5rem)] font-black uppercase leading-[1.05] tracking-[-0.025em]">Brands we create with</h2>
                </div>
                <div class="flex shrink-0 items-center gap-2" aria-label="Client carousel controls">
                    <button type="button" class="flex size-11 items-center justify-center rounded-full border border-white/12 text-white/55 transition hover:border-white/30 hover:bg-white/5 hover:text-white" data-client-carousel-prev aria-label="Previous client">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" /></svg>
                    </button>
                    <button type="button" class="flex size-11 items-center justify-center rounded-full border border-white/12 text-white/55 transition hover:border-white/30 hover:bg-white/5 hover:text-white" data-client-carousel-next aria-label="Next client">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" /></svg>
                    </button>
                </div>
            </div>

            <div
                class="-mx-6 mt-12 flex cursor-grab snap-x snap-mandatory scroll-smooth gap-5 overflow-x-auto px-6 pb-5 select-none [scrollbar-width:none] active:cursor-grabbing [&::-webkit-scrollbar]:hidden sm:-mx-10 sm:px-10 lg:-mx-20 lg:px-20"
                data-client-carousel
                data-reveal
                aria-label="Client logos"
            >
                @forelse ($clients as $client)
                    <article class="flex h-52 w-[260px] min-w-[260px] snap-start items-center justify-center px-6 py-8 sm:h-56 sm:w-[320px] sm:min-w-[320px] sm:px-8" data-client-carousel-item>
                        @if ($client->website_url)
                            <a href="{{ $client->website_url }}" target="_blank" rel="noopener noreferrer" class="flex h-28 w-52 items-center justify-center sm:h-32 sm:w-60" aria-label="Visit {{ $client->name }} website">
                        @endif
                            <img src="{{ $client->logo }}" alt="{{ $client->name }}" class="h-28 w-52 object-contain sm:h-32 sm:w-60" loading="lazy" decoding="async" draggable="false">
                        @if ($client->website_url)
                            </a>
                        @endif
                    </article>
                @empty
                    <p class="w-full py-16 text-center text-sm text-white/35">Client logos are coming soon.</p>
                @endforelse
            </div>

            <div class="mt-20 flex flex-col gap-8 border-t border-white/8 pt-10 sm:flex-row sm:items-center sm:justify-between" data-reveal>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/30">Have a project in mind?</p>
                    <h2 class="mt-3 text-2xl font-black uppercase tracking-[-0.02em] sm:text-3xl">Let’s create something remarkable.</h2>
                </div>
                <a href="{{ route('contact') }}" class="inline-flex shrink-0 items-center justify-center rounded px-7 py-3.5 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" wire:navigate.hover>Start a Project</a>
            </div>
        </div>
    </section>
</main>
