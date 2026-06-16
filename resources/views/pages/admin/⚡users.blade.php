@php
    use App\Models\User;

    $users = User::query()
        ->latest()
        ->limit(50)
        ->get()
        ->map(fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'initials' => $user->initials(),
            'created_at' => $user->created_at?->format('M j, Y'),
        ])
        ->values();
@endphp

<x-layouts::app :title="__('Users')">
<div
    class="min-h-full bg-[#0a0a0c] text-white"
    data-admin-users
    data-index-url="{{ route('admin.users.index') }}"
    data-check-email-url="{{ route('admin.users.check-email') }}"
    data-store-url="{{ route('admin.users.store') }}"
    data-update-url-template="{{ url('admin/users/__USER__') }}"
    data-delete-url-template="{{ url('admin/users/__USER__') }}"
>
    <script type="application/json" data-admin-users-initial>@json($users)</script>

    <div class="space-y-6">
        <section class="border border-[#366bc3]/18 bg-[#0f0f18] p-5 lg:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.26em] text-white/35">{{ __('Admin') }}</p>
                    <h1 class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-[clamp(2.25rem,5vw,3.5rem)] font-black uppercase leading-none tracking-[-0.03em] text-transparent">{{ __('Users') }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-white/45">{{ __('Manage backend access for the Vidhya Studio admin portal.') }}</p>
                </div>

                <button type="button" class="inline-flex rounded px-7 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-users-create>
                    {{ __('New User') }}
                </button>
            </div>
        </section>

        <section class="hidden border border-white/8 bg-[#0d0d13] p-6" data-admin-users-form-shell>
            <div class="mb-5 flex items-center justify-between gap-4">
                <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white" data-admin-users-form-title>{{ __('Create User') }}</h2>
                <button type="button" class="text-xs font-medium uppercase tracking-[0.12em] text-white/38 transition hover:text-white" data-admin-users-cancel>{{ __('Cancel') }}</button>
            </div>

            <form class="grid gap-4 lg:grid-cols-[1fr_1fr_1fr_12rem_auto] lg:items-start" data-admin-users-form>
                <input type="hidden" name="id" data-admin-users-id>

                <div>
                    <input name="name" placeholder="{{ __('Name') }}" autocomplete="name" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-users-field="name">
                    <p class="mt-2 hidden text-xs text-red-400" data-admin-users-error="name"></p>
                </div>

                <div>
                    <input name="email" placeholder="{{ __('Email') }}" type="email" autocomplete="email" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-users-field="email">
                    <p class="mt-2 hidden text-xs text-red-400" data-admin-users-error="email"></p>
                </div>

                <div>
                    <input name="password" placeholder="{{ __('Password') }}" type="password" autocomplete="new-password" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-users-field="password">
                    <p class="mt-2 hidden text-xs text-red-400" data-admin-users-error="password"></p>
                </div>

                <div>
                    <select name="role" class="w-full rounded border border-white/10 bg-[#111118] px-4 py-3.5 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-users-field="role">
                        <option value="user">{{ __('User') }}</option>
                        <option value="admin">{{ __('Admin') }}</option>
                    </select>
                    <p class="mt-2 hidden text-xs text-red-400" data-admin-users-error="role"></p>
                </div>

                <button type="submit" class="inline-flex items-center justify-center gap-3 rounded px-7 py-3.5 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-users-save>
                    <svg class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true" data-admin-users-spinner>
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span data-admin-users-save-label>{{ __('Save') }}</span>
                </button>
            </form>
        </section>

        <section class="border border-white/8 bg-[#0d0d13]">
            <div class="flex flex-col gap-4 border-b border-white/8 p-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('User List') }}</h2>
                <input placeholder="{{ __('Search users') }}" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3] sm:max-w-xs" data-admin-users-search>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="border-b border-white/8 text-[11px] uppercase tracking-[0.16em] text-white/35">
                        <tr>
                            <th class="px-5 py-4 font-semibold">{{ __('Name') }}</th>
                            <th class="px-5 py-4 font-semibold">{{ __('Email') }}</th>
                            <th class="px-5 py-4 font-semibold">{{ __('Role') }}</th>
                            <th class="px-5 py-4 font-semibold">{{ __('Created') }}</th>
                            <th class="px-5 py-4 text-right font-semibold">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/7" data-admin-users-table></tbody>
                </table>
            </div>
        </section>

        <div class="vidhya-admin-modal-backdrop fixed inset-0 z-[9000] hidden place-items-center bg-black/65 px-4" data-admin-users-delete-modal data-admin-modal>
            <div class="vidhya-admin-modal w-full max-w-lg space-y-6 rounded-lg p-6" role="dialog" aria-modal="true" aria-labelledby="delete-user-title">
                <div>
                    <h2 id="delete-user-title" class="text-lg font-black uppercase tracking-[0.04em] text-white">{{ __('Delete user?') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-white/45">{{ __('This action cannot be undone. The selected user will permanently lose admin access.') }}</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" class="rounded border border-white/10 px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white/58 transition hover:border-white/25 hover:text-white" data-admin-users-delete-cancel data-admin-modal-cancel>{{ __('Cancel') }}</button>

                    <button type="button" class="inline-flex items-center justify-center gap-3 rounded px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #823665, #b4143c, #e60012);" data-admin-users-delete-confirm>
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts::app>
