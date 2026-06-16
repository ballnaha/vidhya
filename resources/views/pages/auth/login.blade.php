<x-layouts::auth :title="__('Admin Login')">
    <div class="border border-[#366bc3]/18 bg-[#0f0f18]/90 p-7 shadow-2xl shadow-black/35 backdrop-blur-xl sm:p-8">
        <div class="mb-7 text-center">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-white/35">{{ __('Admin Portal') }}</p>
            <h1 lang="th" class="font-thai mt-3 bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-3xl font-bold tracking-normal text-transparent">{{ __('เข้าสู่ระบบ') }}</h1>
        </div>

        @if ($errors->any())
            <div
                data-toast-on-load
                data-toast-variant="danger"
                data-toast-heading="{{ __('Login failed') }}"
                data-toast-text="{{ $errors->first() }}"
            ></div>
        @endif

        <x-auth-session-status class="mb-5 text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf

            <input
                id="email"
                name="email"
                value="{{ old('email') }}"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="{{ __('Username') }}"
                class="w-full rounded border bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3] @error('email') border-red-500 focus:border-red-500 @else border-white/10 @enderror"
            >

            <input
                id="password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
                placeholder="{{ __('Password') }}"
                class="w-full rounded border bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3] @error('password') border-red-500 focus:border-red-500 @else border-white/10 @enderror"
            >

            <button type="submit" class="inline-flex w-full items-center justify-center gap-3 rounded px-8 py-4 text-sm font-bold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-test="login-button" :disabled="submitting">
                <svg x-show="submitting" x-cloak class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span x-text="submitting ? '{{ __('Loading...') }}' : '{{ __('Log in') }}'">{{ __('Log in') }}</span>
            </button>
        </form>
    </div>
</x-layouts::auth>
