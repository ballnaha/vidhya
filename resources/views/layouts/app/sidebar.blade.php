<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <script>
            window.localStorage.setItem('flux.appearance', 'dark');
            document.documentElement.classList.add('dark');
        </script>
    </head>
    <body class="min-h-screen bg-[#0a0a0c] text-white vidhya-admin-body">
        <div class="vidhya-admin-mobile-backdrop fixed inset-0 z-40 bg-black/55 lg:hidden" data-admin-sidebar-close></div>

        <aside class="vidhya-admin-sidebar fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-e border-white/8 bg-[#0a0a0c] p-4 max-lg:-translate-x-full lg:translate-x-0" data-admin-sidebar>
            <div class="relative z-10 mb-4 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-2 py-1" wire:navigate.hover>
                    <img src="/images/vidhya-studio-logo-ui.png" alt="Vidhya Studio" class="h-7 w-auto object-contain" width="720" height="181">
                    <span class="rounded border border-white/10 px-2 py-1 text-[9px] font-semibold uppercase tracking-[0.16em] text-white/35">Admin</span>
                    <span class="sr-only">{{ __('Dashboard') }}</span>
                </a>
                <button type="button" class="rounded border border-white/10 px-3 py-2 text-sm text-white/55 lg:hidden" data-admin-sidebar-close aria-label="{{ __('Close menu') }}">×</button>
            </div>

            <nav class="relative z-10">
                <div class="grid" data-admin-sidebar-group>
                    <div class="vidhya-admin-sidebar__group-heading">{{ __('Studio') }}</div>
                    <a class="vidhya-sidebar-item {{ request()->routeIs('dashboard') ? 'is-current' : '' }}" href="{{ route('dashboard') }}" wire:navigate.hover data-admin-sidebar-link>
                        <span aria-hidden="true">⌂</span>
                        {{ __('Dashboard') }}
                    </a>
                    <a class="vidhya-sidebar-item {{ request()->routeIs('admin.services') ? 'is-current' : '' }}" href="{{ route('admin.services') }}" wire:navigate.hover data-admin-sidebar-link>
                        <span aria-hidden="true">⚙</span>
                        {{ __('Services') }}
                    </a>
                    <a class="vidhya-sidebar-item {{ request()->routeIs('admin.portfolios') ? 'is-current' : '' }}" href="{{ route('admin.portfolios') }}" wire:navigate.hover data-admin-sidebar-link>
                        <span aria-hidden="true">■</span>
                        {{ __('Portfolio') }}
                    </a>
                    <a class="vidhya-sidebar-item {{ request()->routeIs('admin.directors') ? 'is-current' : '' }}" href="{{ route('admin.directors') }}" wire:navigate.hover data-admin-sidebar-link>
                        <span aria-hidden="true">☼</span>
                        {{ __('Directors') }}
                    </a>
                    
                    <a class="vidhya-sidebar-item {{ request()->routeIs('admin.faqs') ? 'is-current' : '' }}" href="{{ route('admin.faqs') }}" wire:navigate.hover data-admin-sidebar-link>
                        <span aria-hidden="true">?</span>
                        {{ __('FAQ') }}
                    </a>
                    
                </div>

                <div class="mt-5 grid" data-admin-sidebar-group>
                    <div class="vidhya-admin-sidebar__group-heading">{{ __('Setting') }}</div>
                    <a class="vidhya-sidebar-item {{ request()->routeIs('admin.users') ? 'is-current' : '' }}" href="{{ route('admin.users') }}" wire:navigate.hover data-admin-sidebar-link>
                        <span aria-hidden="true">◎</span>
                        {{ __('Users') }}
                    </a>
                </div>
            </nav>

            <div class="flex-1"></div>

            <nav class="relative z-10">
                <a class="vidhya-sidebar-item" href="{{ route('home') }}" wire:navigate.hover data-admin-sidebar-link>
                    <span aria-hidden="true">↗</span>
                    {{ __('View Site') }}
                </a>
            </nav>

            <x-desktop-user-menu class="vidhya-user-menu relative z-10 hidden lg:block" :name="auth()->user()->name" />
        </aside>

        <!-- Mobile User Menu -->
        <header class="flex min-h-14 items-center justify-between border-b border-white/8 bg-[#0d0d13] px-4 lg:hidden">
            <button type="button" class="rounded border border-white/10 px-3 py-2 text-sm text-white/70" data-admin-sidebar-open aria-label="{{ __('Open menu') }}">☰</button>
            <x-desktop-user-menu class="w-52" :name="auth()->user()->name" />
        </header>

        <div class="lg:pl-72">
            {{ $slot }}
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
