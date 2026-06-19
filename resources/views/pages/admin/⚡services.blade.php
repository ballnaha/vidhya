@php
    use App\Models\Service;

    $services = Service::query()
        ->orderBy('sort_order')
        ->get()
        ->map(fn (Service $service) => [
            'id' => $service->id,
            'num' => $service->num,
            'title' => $service->title,
            'description' => $service->description,
            'bullets_raw' => implode("\n", $service->bullets ?? []),
            'bullets' => $service->bullets ?? [],
            'accent' => $service->accent,
            'image' => $service->image,
            'sort_order' => $service->sort_order,
            'created_at' => $service->created_at?->format('M j, Y'),
        ])
        ->values();
@endphp

<x-layouts::app :title="__('Services')">
<div
    class="min-h-full bg-[#0a0a0c] text-white"
    data-admin-services
    data-index-url="{{ route('admin.services.index') }}"
    data-store-url="{{ route('admin.services.store') }}"
    data-update-url-template="{{ url('admin/services/__SERVICE__') }}"
    data-delete-url-template="{{ url('admin/services/__SERVICE__') }}"
    data-reorder-url="{{ route('admin.services.reorder') }}"
>
    <script type="application/json" data-admin-services-initial>@json($services)</script>

    <div class="space-y-6">
        <section class="border border-[#366bc3]/18 bg-[#0f0f18] p-5 lg:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.26em] text-white/35">{{ __('Admin') }}</p>
                    <h1 class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-[clamp(2.25rem,5vw,3.5rem)] font-black uppercase leading-none tracking-[-0.03em] text-transparent">{{ __('Services') }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-white/45">{{ __('Manage the services list displayed on the marketing website.') }}</p>
                </div>

                <button type="button" class="inline-flex rounded px-7 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-services-create>
                    {{ __('New Service') }}
                </button>
            </div>
        </section>

        <section class="hidden border border-white/8 bg-[#0d0d13] p-6" data-admin-services-form-shell>
            <div class="mb-5 flex items-center justify-between gap-4 border-b border-white/8 pb-3">
                <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white" data-admin-services-form-title>{{ __('Create Service') }}</h2>
                <button type="button" class="text-xs font-medium uppercase tracking-[0.12em] text-white/38 transition hover:text-white" data-admin-services-cancel>{{ __('Cancel') }}</button>
            </div>

            <form class="space-y-6" data-admin-services-form enctype="multipart/form-data">
                <input type="hidden" name="id" data-admin-services-id>

                <div class="grid gap-6 md:grid-cols-4">
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Service No.') }}</label>
                        <input name="num" placeholder="01" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-services-field="num">
                        <p class="mt-2 hidden text-xs text-red-400" data-admin-services-error="num"></p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Title') }}</label>
                        <input name="title" placeholder="AI POCs & Previs" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-services-field="title">
                        <p class="mt-2 hidden text-xs text-red-400" data-admin-services-error="title"></p>
                    </div>

                    <div>
                        <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Accent Color (Hex)') }}</label>
                        <div class="flex gap-2.5">
                            <input name="accent" placeholder="#366bc3" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-services-field="accent">
                        </div>
                        <p class="mt-2 hidden text-xs text-red-400" data-admin-services-error="accent"></p>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Description') }}</label>
                            <textarea name="description" rows="4" placeholder="Service description..." class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-services-field="description"></textarea>
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-services-error="description"></p>
                        </div>
                        
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Bullet Highlights (One bullet point per line)') }}</label>
                            <textarea name="bullets_raw" rows="4" placeholder="Concept validation&#10;Moodboard & storyboard&#10;Pitch-ready materials" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-services-field="bullets_raw"></textarea>
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-services-error="bullets_raw"></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Cover Image') }}</label>
                            <div class="relative w-full aspect-video bg-black/40 border border-dashed border-white/12 rounded overflow-hidden group/uploader hover:border-[#366bc3]/50 transition cursor-pointer flex flex-col items-center justify-center text-center p-3" data-admin-services-upload-card>
                                <!-- Hidden file input -->
                                <input type="file" name="image_file" accept="image/*" class="hidden" data-admin-services-file-input>
                                
                                <!-- Hidden text input for existing image path -->
                                <input type="hidden" name="image" data-admin-services-field="image">

                                <!-- Placeholder -->
                                <div class="flex flex-col items-center gap-1.5 text-white/35 transition group-hover/uploader:text-white/60" data-admin-services-upload-placeholder>
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-[10px] font-semibold uppercase tracking-wider">{{ __('Upload Cover') }}</span>
                                </div>

                                <!-- Preview Wrapper -->
                                <div class="absolute inset-0 hidden" data-admin-services-preview-wrapper>
                                    <img src="" class="h-full w-full object-cover" data-admin-services-preview>
                                    <!-- Hover Action Overlay -->
                                    <div class="absolute inset-0 bg-black/75 opacity-0 group-hover/uploader:opacity-100 transition-opacity flex items-center justify-center gap-3 z-10">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-white bg-white/10 px-2.5 py-1 rounded border border-white/10">{{ __('Change') }}</span>
                                        <button type="button" class="relative z-20 p-2 rounded bg-red-500/80 text-white hover:bg-red-500 transition shadow" data-admin-services-remove-image-file title="{{ __('Delete Image') }}">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-services-error="image_file"></p>
                        </div>

                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Priority (Auto)') }}</label>
                            <input name="sort_order" type="number" min="0" readonly aria-readonly="true" tabindex="-1" class="w-28 cursor-not-allowed rounded border border-white/8 bg-black/25 px-4 py-3.5 text-sm text-white/45 outline-none" data-admin-services-field="sort_order">
                            <p class="mt-2 text-[11px] text-white/30">{{ __('Managed automatically by Drag & Drop.') }}</p>
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-services-error="sort_order"></p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-white/8">
                    <button type="submit" class="inline-flex items-center justify-center gap-3 rounded px-8 py-3.5 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-services-save>
                        <svg class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true" data-admin-services-spinner>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span data-admin-services-save-label>{{ __('Save Service') }}</span>
                    </button>
                </div>
            </form>
        </section>

        <section class="border border-white/8 bg-[#0d0d13]">
            <div class="flex flex-col gap-4 border-b border-white/8 p-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Service List') }}</h2>
                <input placeholder="{{ __('Search services') }}" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3] sm:max-w-xs" data-admin-services-search>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="border-b border-white/8 text-[11px] uppercase tracking-[0.16em] text-white/35">
                        <tr>
                            <th class="px-5 py-4 w-10"></th>
                            <th class="px-5 py-4 font-semibold w-24">{{ __('Image') }}</th>
                            <th class="px-5 py-4 font-semibold w-20">{{ __('No.') }}</th>
                            <th class="px-5 py-4 font-semibold w-1/4">{{ __('Title') }}</th>
                            <th class="px-5 py-4 font-semibold w-1/3">{{ __('Description') }}</th>
                            <th class="px-5 py-4 font-semibold w-20">{{ __('Order') }}</th>
                            <th class="px-5 py-4 text-right font-semibold w-32">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/7" data-admin-services-table></tbody>
                </table>
            </div>
        </section>

        <div class="vidhya-admin-modal-backdrop fixed inset-0 z-[9000] hidden place-items-center bg-black/65 px-4" data-admin-services-delete-modal data-admin-modal>
            <div class="vidhya-admin-modal w-full max-w-lg space-y-6 rounded-lg p-6" role="dialog" aria-modal="true" aria-labelledby="delete-service-title">
                <div>
                    <h2 id="delete-service-title" class="text-lg font-black uppercase tracking-[0.04em] text-white">{{ __('Delete Service?') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-white/45">{{ __('Are you sure you want to delete this service? It will be immediately removed from the public services page.') }}</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" class="rounded border border-white/10 px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white/58 transition hover:border-white/25 hover:text-white" data-admin-services-delete-cancel data-admin-modal-cancel>{{ __('Cancel') }}</button>

                    <button type="button" class="inline-flex items-center justify-center gap-3 rounded px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #823665, #b4143c, #e60012);" data-admin-services-delete-confirm>
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts::app>
