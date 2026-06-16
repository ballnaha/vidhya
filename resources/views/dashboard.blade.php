<x-layouts::app :title="__('Dashboard')">
    <div class="min-h-full bg-[#0a0a0c] text-white">
        <div class="pointer-events-none fixed right-[-120px] top-[-120px] h-[420px] w-[420px] bg-[radial-gradient(ellipse,rgba(230,0,18,0.12)_0%,transparent_65%)]"></div>

        <div class="relative z-10 space-y-6">
            <section class="border border-[#366bc3]/18 bg-[#0f0f18] p-5 lg:p-6">
                <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.26em] text-white/35">{{ __('Admin') }}</p>
                <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
                    <div>
                        <h1 class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-[clamp(2.25rem,5vw,3.5rem)] font-black uppercase leading-none tracking-[-0.03em] text-transparent">{{ __('Studio Control') }}</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-white/45">{{ __('A focused backend workspace for managing Vidhya Studio content.') }}</p>
                    </div>
                    <a href="{{ route('home') }}" class="inline-flex rounded px-7 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" wire:navigate.hover>
                        {{ __('View Site') }}
                    </a>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-3">
                @foreach ([
                    ['label' => 'Contact Leads', 'value' => 'Ready', 'accent' => '#366bc3'],
                    ['label' => 'Content System', 'value' => 'Online', 'accent' => '#823665'],
                    ['label' => 'Brand Portal', 'value' => 'Active', 'accent' => '#e60012'],
                ] as $metric)
                    <article class="border-t-2 bg-[#0d0d13] px-6 py-6" style="border-color: {{ $metric['accent'] }};">
                        <p class="text-[11px] font-medium uppercase tracking-[0.16em] text-white/35">{{ __($metric['label']) }}</p>
                        <p class="mt-3 text-2xl font-black uppercase tracking-[-0.02em] text-white">{{ __($metric['value']) }}</p>
                    </article>
                @endforeach
            </section>

            <section class="grid gap-4 lg:grid-cols-2">
                <article class="border border-white/8 bg-[#0d0d13] p-7">
                    <h2 class="mb-3 text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Quick Actions') }}</h2>
                    <div class="space-y-3">
                        <a href="{{ route('contact') }}" class="block rounded border border-white/8 px-4 py-3 text-sm text-white/45 transition hover:border-white/20 hover:bg-white/[0.04] hover:text-white" wire:navigate.hover>{{ __('Review contact page') }}</a>
                        <a href="{{ route('services') }}" class="block rounded border border-white/8 px-4 py-3 text-sm text-white/45 transition hover:border-white/20 hover:bg-white/[0.04] hover:text-white" wire:navigate.hover>{{ __('Review services page') }}</a>
                    </div>
                </article>

                <article class="border-l-[3px] border-white/8 bg-[#0d0d13] p-7">
                    <h2 class="mb-3 text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Vidhya Standard') }}</h2>
                    <p class="text-sm leading-8 text-white/42">{{ __('Keep the backend calm, dense, and operational while preserving the same cinematic identity as the public experience.') }}</p>
                    <div class="mt-6 h-[3px] w-20 bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012]"></div>
                </article>
            </section>
        </div>
    </div>
</x-layouts::app>
