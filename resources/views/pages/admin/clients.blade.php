@php
    $clientData = $clients->map(fn ($client) => [
        'id' => $client->id,
        'name' => $client->name,
        'logo' => $client->logo,
        'is_active' => (bool) $client->is_active,
        'sort_order' => $client->sort_order,
        'created_at' => $client->created_at?->format('M j, Y'),
    ])->values();
@endphp

<x-layouts::app :title="__('Clients')">
<div class="min-h-full bg-[#0a0a0c] text-white"
    data-admin-clients
    data-index-url="{{ route('admin.clients.data') }}"
    data-store-url="{{ route('admin.clients.store') }}"
    data-reorder-url="{{ route('admin.clients.reorder') }}"
    data-update-url-template="{{ url('admin/clients/__CLIENT__') }}"
    data-delete-url-template="{{ url('admin/clients/__CLIENT__') }}">
    <script type="application/json" data-admin-clients-initial>@json($clientData)</script>

    <div class="space-y-6">
        <section class="border border-[#366bc3]/18 bg-[#0f0f18] p-5 lg:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.26em] text-white/35">{{ __('Admin') }}</p>
                    <h1 class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-[clamp(2.25rem,5vw,3.5rem)] font-black uppercase leading-none tracking-[-0.03em] text-transparent">{{ __('Clients') }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-white/45">{{ __('Manage client logos displayed in the carousel on the public Client page.') }}</p>
                </div>
                <button type="button" class="inline-flex rounded px-7 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110" style="background:linear-gradient(90deg,#366bc3,#823665,#e60012)" data-admin-clients-create>{{ __('New Client') }}</button>
            </div>
        </section>

        <section class="hidden border border-white/8 bg-[#0d0d13] p-6" data-admin-clients-form-shell>
            <div class="mb-5 flex items-center justify-between gap-4 border-b border-white/8 pb-3">
                <h2 class="text-sm font-black uppercase tracking-[0.05em]" data-admin-clients-form-title>{{ __('Create Client') }}</h2>
                <button type="button" class="text-xs font-medium uppercase tracking-[0.12em] text-white/38 hover:text-white" data-admin-clients-cancel>{{ __('Cancel') }}</button>
            </div>
            <form class="space-y-6" data-admin-clients-form enctype="multipart/form-data">
                <input type="hidden" name="id" data-admin-clients-id>
                <input type="hidden" name="logo" data-admin-clients-field="logo">
                <div class="grid gap-6 lg:grid-cols-[1fr_1fr_220px]">
                    <div class="space-y-5">
                        <div><label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-white/45">{{ __('Client Name') }}</label><input name="name" placeholder="Suntory Boss Coffee" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm outline-none focus:border-[#366bc3]" data-admin-clients-field="name"><p class="mt-2 hidden text-xs text-red-400" data-admin-clients-error="name"></p></div>
                        <div class="flex items-end gap-5"><div><label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-white/45">{{ __('Priority (Auto)') }}</label><input name="sort_order" type="number" min="0" readonly aria-readonly="true" tabindex="-1" class="w-28 cursor-not-allowed rounded border border-white/8 bg-black/25 px-4 py-3.5 text-sm text-white/45 outline-none" data-admin-clients-field="sort_order"><p class="mt-2 text-[11px] text-white/30">{{ __('Managed automatically by Drag & Drop.') }}</p><p class="mt-2 hidden text-xs text-red-400" data-admin-clients-error="sort_order"></p></div><label class="flex items-center gap-2 pb-8 text-xs font-semibold uppercase tracking-wider text-white/55"><input name="is_active" type="checkbox" value="1" checked> {{ __('Active') }}</label></div>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-white/45">{{ __('Client Logo') }}</label>
                        <div class="group relative flex aspect-video cursor-pointer items-center justify-center overflow-hidden rounded border border-dashed border-white/12 bg-black/30 p-5 hover:border-[#366bc3]/50" data-admin-clients-upload-card>
                            <input type="file" name="logo_file" accept="image/png,image/jpeg,image/webp" class="hidden" data-admin-clients-file-input>
                            <div class="text-center text-white/35" data-admin-clients-upload-placeholder><svg class="mx-auto size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16V4m0 0L7 9m5-5 5 5M5 14v5h14v-5"/></svg><span class="mt-2 block text-[10px] font-semibold uppercase tracking-wider">{{ __('Upload Logo') }}</span></div>
                            <img src="" alt="" class="hidden max-h-full max-w-full object-contain" data-admin-clients-preview>
                        </div>
                        <p class="mt-2 text-[11px] text-white/30">PNG, JPG or WebP · Maximum 5 MB</p><p class="mt-2 hidden text-xs text-red-400" data-admin-clients-error="logo_file"></p>
                    </div>
                    <div class="flex items-end justify-end">
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-3 rounded px-8 py-3.5 text-xs font-semibold uppercase tracking-[0.1em] transition hover:brightness-110 disabled:opacity-60" style="background:linear-gradient(90deg,#366bc3,#823665,#e60012)" data-admin-clients-save><svg class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none" data-admin-clients-spinner><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg><span data-admin-clients-save-label>{{ __('Save Client') }}</span></button>
                    </div>
                </div>
            </form>
        </section>

        <section class="border border-white/8 bg-[#0d0d13]">
            <div class="flex flex-col gap-4 border-b border-white/8 p-5 sm:flex-row sm:items-center sm:justify-between"><h2 class="text-sm font-black uppercase tracking-[0.05em]">{{ __('Client List') }}</h2><input placeholder="{{ __('Search clients') }}" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm outline-none placeholder:text-white/28 focus:border-[#366bc3] sm:max-w-xs" data-admin-clients-search></div>
            <div class="overflow-x-auto"><table class="w-full min-w-[760px] text-left text-sm"><thead class="border-b border-white/8 text-[11px] uppercase tracking-[0.16em] text-white/35"><tr><th class="w-10 px-5 py-4"></th><th class="px-5 py-4 font-semibold">{{ __('Logo') }}</th><th class="px-5 py-4 font-semibold">{{ __('Client') }}</th><th class="px-5 py-4 font-semibold">{{ __('Active') }}</th><th class="px-5 py-4 font-semibold">{{ __('Created') }}</th><th class="px-5 py-4 font-semibold">{{ __('Order') }}</th><th class="px-5 py-4 text-right font-semibold">{{ __('Actions') }}</th></tr></thead><tbody class="divide-y divide-white/7" data-admin-clients-table>
                @foreach ($clients as $client)
                    <tr class="transition hover:bg-white/[0.035]" data-admin-clients-row data-client-id="{{ $client->id }}">
                        <td class="w-16 px-3 py-2 text-center"><button type="button" class="inline-flex size-10 touch-none select-none items-center justify-center rounded-md border border-transparent text-white/35 transition hover:border-white/10 hover:bg-white/[0.06] hover:text-white/75 active:cursor-grabbing active:bg-white/10 cursor-grab" data-admin-clients-drag-handle aria-label="Drag to reorder"><span class="pointer-events-none text-base leading-none">⋮⋮</span></button></td>
                        <td class="px-5 py-4"><img src="{{ $client->logo }}" alt="{{ $client->name }}" class="h-14 w-24 object-contain"></td>
                        <td class="px-5 py-4 font-semibold">{{ $client->name }}</td>
                        <td class="px-5 py-4">{{ $client->is_active ? __('Yes') : __('No') }}</td>
                        <td class="px-5 py-4 text-xs text-white/35">{{ $client->created_at?->format('M j, Y') }}</td>
                        <td class="px-5 py-4 font-mono text-xs text-white/35">{{ $client->sort_order }}</td>
                        <td class="px-5 py-4 text-right" aria-hidden="true"></td>
                    </tr>
                @endforeach
            </tbody></table></div>
        </section>

        <div class="vidhya-admin-modal-backdrop fixed inset-0 z-[9000] hidden place-items-center bg-black/65 px-4" data-admin-clients-delete-modal data-admin-modal><div class="vidhya-admin-modal w-full max-w-lg space-y-6 rounded-lg p-6" role="dialog" aria-modal="true"><div><h2 class="text-lg font-black uppercase tracking-[0.04em]">{{ __('Delete Client?') }}</h2><p class="mt-3 text-sm leading-7 text-white/45">{{ __('This logo will be removed from the public Client page immediately.') }}</p></div><div class="flex justify-end gap-3"><button type="button" class="rounded border border-white/10 px-5 py-3 text-xs font-semibold uppercase text-white/58" data-admin-clients-delete-cancel data-admin-modal-cancel>{{ __('Cancel') }}</button><button type="button" class="rounded px-5 py-3 text-xs font-semibold uppercase" style="background:linear-gradient(90deg,#823665,#b4143c,#e60012)" data-admin-clients-delete-confirm>{{ __('Delete') }}</button></div></div></div>
    </div>
</div>
</x-layouts::app>
