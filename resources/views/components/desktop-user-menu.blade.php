<div class="relative {{ $attributes->get('class') }}" data-admin-user-menu>
    <button type="button" class="vidhya-admin-user-trigger flex w-full items-center gap-3 rounded px-3 py-2 text-left transition" data-admin-user-menu-toggle aria-expanded="false" data-test="sidebar-menu-button">
        <span class="flex size-9 shrink-0 items-center justify-center rounded border border-white/10 bg-white/[0.04] text-xs font-semibold uppercase text-white/75">{{ auth()->user()->initials() }}</span>
        <span class="grid min-w-0 flex-1 text-sm leading-tight">
            <span class="truncate font-semibold text-white">{{ auth()->user()->name }}</span>
            <span class="truncate text-xs text-white/40">{{ auth()->user()->email }}</span>
        </span>
        <span class="text-white/35" aria-hidden="true">⌄</span>
    </button>

    <div class="vidhya-admin-user-dropdown absolute bottom-full left-0 z-50 mb-2 w-full" data-admin-user-menu-panel>
        <div class="vidhya-admin-user-dropdown__header flex items-center gap-3 px-3 py-3 text-start text-sm">
            <span class="flex size-9 shrink-0 items-center justify-center rounded border border-white/10 bg-white/[0.04] text-xs font-semibold uppercase text-white/75">{{ auth()->user()->initials() }}</span>
            <div class="grid flex-1 text-start text-sm leading-tight">
                <span class="truncate font-semibold text-white">{{ auth()->user()->name }}</span>
                <span class="truncate text-white/40">{{ auth()->user()->email }}</span>
            </div>
        </div>
        <div class="mx-2 h-px bg-white/8"></div>
        <form method="POST" action="{{ route('logout') }}" class="w-full p-1.5">
            @csrf
            <button type="submit" class="vidhya-admin-user-dropdown__item vidhya-admin-user-dropdown__item--danger w-full cursor-pointer rounded px-3 py-2.5 text-left text-sm" data-test="logout-button">
                {{ __('Log out') }}
            </button>
        </form>
    </div>
</div>
