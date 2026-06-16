<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#0a0a0c] text-white antialiased">
        <div class="relative flex min-h-svh items-center justify-center overflow-hidden px-6 py-16 sm:px-10">
            <div class="pointer-events-none absolute left-[-160px] top-[-160px] h-[460px] w-[460px] bg-[radial-gradient(ellipse,rgba(54,107,195,0.22)_0%,transparent_65%)]"></div>
            <div class="pointer-events-none absolute bottom-[-180px] right-[-120px] h-[520px] w-[520px] bg-[radial-gradient(ellipse,rgba(230,0,18,0.16)_0%,transparent_68%)]"></div>
            <div class="pointer-events-none absolute right-[-20px] top-1/2 hidden -translate-y-1/2 select-none text-[clamp(180px,28vw,360px)] font-black leading-none tracking-[-0.05em] text-white/[0.025] lg:block">VS</div>

            <div class="relative z-10 grid w-full max-w-6xl gap-12 lg:grid-cols-[1fr_460px] lg:items-center">
                <div class="hidden lg:block">
                    <a href="{{ route('home') }}" class="inline-flex" wire:navigate>
                        <img src="/images/vidhya-studio-logo-ui.png" alt="Vidhya Studio" class="h-9 w-auto object-contain" width="720" height="181">
                    </a>

                    <p class="mt-14 text-[11px] font-semibold uppercase tracking-[0.28em] text-white/35">Admin Portal</p>
                    <h1 class="mt-5 max-w-3xl text-[clamp(3rem,5vw,5rem)] font-black uppercase leading-[1.05] tracking-[-0.03em]">
                        <span class="block bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-transparent">Cinematic</span>
                        <span class="block text-white">Control Room.</span>
                    </h1>
                    <p class="mt-7 max-w-xl text-[15px] leading-8 text-white/45">Manage Vidhya Studio operations with the same cinematic discipline, speed, and measurable precision that powers the frontend experience.</p>
                    <div class="mt-10 h-[3px] w-28 bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012]"></div>
                </div>

                <div class="w-full">
                    <a href="{{ route('home') }}" class="mb-10 flex justify-center lg:hidden" wire:navigate>
                        <img src="/images/vidhya-studio-logo-ui.png" alt="Vidhya Studio" class="h-8 w-auto object-contain" width="720" height="181">
                    </a>

                    {{ $slot }}
                </div>
            </div>
        </div>

        @fluxScripts
    </body>
</html>
