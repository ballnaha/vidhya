<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#0a0a0c] text-white antialiased">
        <header class="fixed inset-x-0 top-0 z-50 border-white/0 bg-[#0a0a0c]/82 backdrop-blur-xl" data-mobile-menu-shell>
            <div class="mx-auto flex h-[72px] max-w-[1800px] items-center justify-between px-6 sm:px-10 lg:px-15">
                <a href="{{ route('home') }}" wire:navigate.hover aria-label="Vidhya Studio home">
                    <img src="/images/vidhya-studio-logo-ui.png" alt="Vidhya Studio" class="h-7 w-auto object-contain" width="720" height="181" fetchpriority="high">
                </a>

                <nav class="hidden items-center gap-8 text-xs font-medium uppercase tracking-[0.08em] text-white/42 md:flex">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'border-b border-[#366bc3] text-white' : 'hover:text-white' }} pb-1 transition" wire:navigate.hover>Home</a>
                    <a href="{{ route('ai-director') }}" class="{{ request()->routeIs('ai-director') ? 'border-b border-[#366bc3] text-white' : 'hover:text-white' }} pb-1 transition" wire:navigate.hover>AI Director</a>
                    <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'border-b border-[#366bc3] text-white' : 'hover:text-white' }} pb-1 transition" wire:navigate.hover>About</a>
                    <a href="{{ route('services') }}" class="{{ request()->routeIs('services') ? 'border-b border-[#366bc3] text-white' : 'hover:text-white' }} pb-1 transition" wire:navigate.hover>Services</a>
                    <a href="{{ route('contact') }}" class="rounded px-6 py-3 text-[11px] font-semibold text-white transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" wire:navigate.hover>Start a Project</a>
                </nav>

                <button type="button" class="inline-flex min-h-10 items-center gap-3 rounded border border-white/12 px-4 text-[11px] font-medium uppercase tracking-[0.12em] text-white/70 transition hover:border-white/35 hover:text-white md:hidden" aria-expanded="false" aria-controls="marketing-mobile-menu" data-mobile-menu-toggle>
                    <span>Menu</span>
                    <span class="flex h-3.5 w-4 flex-col justify-between" aria-hidden="true">
                        <span class="h-px w-full bg-current transition" data-mobile-menu-line="top"></span>
                        <span class="h-px w-full bg-current transition" data-mobile-menu-line="middle"></span>
                        <span class="h-px w-full bg-current transition" data-mobile-menu-line="bottom"></span>
                    </span>
                </button>
            </div>

            <div class="vidhya-mobile-menu-backdrop fixed inset-0 top-[72px] bg-black/55 md:hidden" aria-hidden="true" data-mobile-menu-close></div>

            <div id="marketing-mobile-menu" class="vidhya-mobile-menu-panel fixed right-0 top-[72px] h-[calc(100dvh-72px)] w-full max-w-80 border-l border-white/10 bg-[#0a0a0c]/98 px-5 py-6 shadow-2xl shadow-black/45 md:hidden" data-mobile-menu-panel>
                <nav class="flex flex-col gap-1 text-sm font-medium uppercase tracking-[0.08em] text-white/50">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'bg-white/6 text-white' : 'hover:bg-white/[0.04] hover:text-white' }} rounded px-4 py-3 transition" data-mobile-menu-close wire:navigate.hover>Home</a>
                    <a href="{{ route('ai-director') }}" class="{{ request()->routeIs('ai-director') ? 'bg-white/6 text-white' : 'hover:bg-white/[0.04] hover:text-white' }} rounded px-4 py-3 transition" data-mobile-menu-close wire:navigate.hover>AI Director</a>
                    <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'bg-white/6 text-white' : 'hover:bg-white/[0.04] hover:text-white' }} rounded px-4 py-3 transition" data-mobile-menu-close wire:navigate.hover>About</a>
                    <a href="{{ route('services') }}" class="{{ request()->routeIs('services') ? 'bg-white/6 text-white' : 'hover:bg-white/[0.04] hover:text-white' }} rounded px-4 py-3 transition" data-mobile-menu-close wire:navigate.hover>Services</a>
                    <a href="{{ route('contact') }}" class="mt-3 rounded px-5 py-3 text-center text-[12px] font-semibold text-white transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-mobile-menu-close wire:navigate.hover>Start a Project</a>
                </nav>
            </div>
        </header>

        {{ $slot }}

        <footer class="border-t border-white/7 bg-[#050507] px-6 py-16 sm:px-10 lg:px-20">
            <div class="mx-auto grid max-w-[1800px] gap-12 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
                <div>
                    <img src="/images/vidhya-studio-logo-ui.png" alt="Vidhya Studio" class="h-9 w-auto" width="720" height="181" loading="lazy">
                    <p class="mt-5 max-w-60 text-sm leading-7 text-white/35">Cinematic perspective. AI speed. Measurable impact.</p>
                    <p class="mt-2 max-w-60 text-xs leading-6 text-white/22">A new venture from Benetone Films.</p>
                    <div class="mt-6 h-[3px] w-15 bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012]"></div>
                </div>

                <div>
                    <h3 class="mb-5 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-white/28">Services</h3>
                    @foreach (['AI POCs & Previs', 'AI Advertising', 'Post Production', 'AI Models & Influencers', 'Micro Drama'] as $item)
                        <a href="{{ route('services') }}" class="mb-2 block text-sm text-white/45 transition hover:text-white" wire:navigate.hover>{{ $item }}</a>
                    @endforeach
                </div>

                <div>
                    <h3 class="mb-5 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-white/28">Company</h3>
                    <a href="{{ route('ai-director') }}" class="mb-2 block text-sm text-white/45 transition hover:text-white" wire:navigate.hover>AI Director</a>
                    <a href="{{ route('about') }}" class="mb-2 block text-sm text-white/45 transition hover:text-white" wire:navigate.hover>About</a>
                    <a href="{{ route('services') }}" class="mb-2 block text-sm text-white/45 transition hover:text-white" wire:navigate.hover>Services</a>
                    <a href="{{ route('contact') }}" class="mb-2 block text-sm text-white/45 transition hover:text-white" wire:navigate.hover>Contact</a>
                </div>

                <div>
                    <h3 class="mb-5 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-white/28">Connect</h3>
                    <a href="{{ route('contact') }}" class="mb-2 block text-sm text-white/45 transition hover:text-white" wire:navigate.hover>Start a Project</a>
                    <a href="{{ route('contact') }}" class="mb-2 block text-sm text-white/45 transition hover:text-white" wire:navigate.hover>Direct Email</a>
                </div>
            </div>

            <div class="mx-auto mt-14 flex max-w-[1800px] flex-col justify-between gap-4 border-t border-white/7 pt-7 text-xs text-white/22 lg:flex-row">
                <span>© 2026 Vidhya Studio. A Benetone Films venture. All rights reserved.</span>
                <span class="font-semibold uppercase tracking-[0.12em] text-white/18">Cinematic Perspective · AI Speed · Measurable Impact</span>
            </div>
        </footer>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
