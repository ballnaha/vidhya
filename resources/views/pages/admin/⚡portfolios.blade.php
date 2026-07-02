@php
    use App\Models\Portfolio;
    use App\Models\Service;

    $portfolios = Portfolio::query()
        ->with('service')
        ->orderBy('sort_order')
        ->get()
        ->map(fn (Portfolio $portfolio) => [
            'id' => $portfolio->id,
            'title' => $portfolio->title,
            'service_id' => $portfolio->service_id,
            'service_title' => $portfolio->service?->title ?? '',
            'video_url' => $portfolio->video_url ?? '',
            'video_aspect_ratio' => $portfolio->video_aspect_ratio ?? '16:9',
            'image' => $portfolio->image,
            'span' => $portfolio->span,
            'show_in_portfolio' => (bool) $portfolio->show_in_portfolio,
            'sort_order' => $portfolio->sort_order,
            'created_at' => $portfolio->created_at?->format('M j, Y'),
        ])
        ->values();

    $services = Service::query()->orderBy('sort_order')->get();
@endphp

<x-layouts::app :title="__('Portfolio Works')">
<div
    class="min-h-full bg-[#0a0a0c] text-white"
    data-admin-portfolios
    data-index-url="{{ route('admin.portfolios.index') }}"
    data-store-url="{{ route('admin.portfolios.store') }}"
    data-update-url-template="{{ url('admin/portfolios/__PORTFOLIO__') }}"
    data-delete-url-template="{{ url('admin/portfolios/__PORTFOLIO__') }}"
    data-reorder-url="{{ route('admin.portfolios.reorder') }}"
>
    <script type="application/json" data-admin-portfolios-initial>@json($portfolios)</script>
    <script type="application/json" data-admin-services-initial>@json($services)</script>

    <div class="space-y-6">
        <section class="border border-[#366bc3]/18 bg-[#0f0f18] p-5 lg:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.26em] text-white/35">{{ __('Admin') }}</p>
                    <h1 class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-[clamp(2.25rem,5vw,3.5rem)] font-black uppercase leading-none tracking-[-0.03em] text-transparent">{{ __('Portfolio') }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-white/45">{{ __('Manage and sort Vidhya Studio\'s portfolio pieces, images, and video links.') }}</p>
                </div>

                <button type="button" class="inline-flex rounded px-7 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-portfolios-create>
                    {{ __('New Portfolio Item') }}
                </button>
            </div>
        </section>

        <section class="hidden border border-white/8 bg-[#0d0d13] p-6" data-admin-portfolios-form-shell>
            <div class="mb-5 flex items-center justify-between gap-4 border-b border-white/8 pb-3">
                <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white" data-admin-portfolios-form-title>{{ __('Create Portfolio Item') }}</h2>
                <button type="button" class="text-xs font-medium uppercase tracking-[0.12em] text-white/38 transition hover:text-white" data-admin-portfolios-cancel>{{ __('Cancel') }}</button>
            </div>

            <form class="space-y-6" data-admin-portfolios-form enctype="multipart/form-data">
                <input type="hidden" name="id" data-admin-portfolios-id>

                <div class="grid gap-6 md:grid-cols-3">
                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Title') }}</label>
                            <input name="title" placeholder="Nike - Rise" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-portfolios-field="title">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-portfolios-error="title"></p>
                        </div>

                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Service Category (Optional)') }}</label>
                            <select name="service_id" class="w-full rounded border border-white/10 bg-[#0d0d13] px-3.5 py-3.5 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-portfolios-field="service_id">
                                <option value="">{{ __('None / General') }}</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->title }}</option>
                                @endforeach
                            </select>
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-portfolios-error="service_id"></p>
                        </div>

                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('YouTube / Vimeo Link (Optional)') }}</label>
                            <input name="video_url" placeholder="https://youtube.com/..." class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-portfolios-field="video_url">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-portfolios-error="video_url"></p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Display Size (Portfolio Page Grid)') }}</label>
                                <select name="span" class="w-full rounded border border-white/10 bg-[#0d0d13] px-3.5 py-3.5 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-portfolios-field="span">
                                    <option value="md:col-span-2">{{ __('1/3 Width') }}</option>
                                    <option value="md:col-span-3">{{ __('1/2 Width') }}</option>
                                    <option value="md:col-span-4">{{ __('2/3 Width') }}</option>
                                    <option value="md:col-span-6">{{ __('Full Width') }}</option>
                                </select>
                                <p class="mt-2 hidden text-xs text-red-400" data-admin-portfolios-error="span"></p>
                            </div>

                            <div>
                                <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Video Ratio') }}</label>
                                <select name="video_aspect_ratio" class="w-full rounded border border-white/10 bg-[#0d0d13] px-3.5 py-3.5 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-portfolios-field="video_aspect_ratio">
                                    <option value="16:9">{{ __('16:9 Landscape') }}</option>
                                    <option value="9:16">{{ __('9:16 Portrait') }}</option>
                                </select>
                                <p class="mt-2 hidden text-xs text-red-400" data-admin-portfolios-error="video_aspect_ratio"></p>
                            </div>

                            <div>
                                <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Priority (Auto)') }}</label>
                                <input name="sort_order" type="number" min="0" readonly aria-readonly="true" tabindex="-1" class="w-full cursor-not-allowed rounded border border-white/8 bg-black/25 px-4 py-3.5 text-sm text-white/45 outline-none" data-admin-portfolios-field="sort_order">
                                <p class="mt-2 text-[11px] text-white/30">{{ __('Managed automatically by Drag & Drop.') }}</p>
                                <p class="mt-2 hidden text-xs text-red-400" data-admin-portfolios-error="sort_order"></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input type="checkbox" id="show_in_portfolio" name="show_in_portfolio" value="1" checked class="h-4 w-4 rounded border-white/10 bg-white/[0.04] text-[#366bc3] focus:ring-[#366bc3]" data-admin-portfolios-field="show_in_portfolio">
                            <label for="show_in_portfolio" class="text-xs font-semibold text-white/65 uppercase tracking-wider cursor-pointer">{{ __('Show in Portfolio Page') }}</label>
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-portfolios-error="show_in_portfolio"></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Cover Image') }}</label>
                            <div class="relative w-full aspect-video bg-black/40 border border-dashed border-white/12 rounded overflow-hidden group/uploader hover:border-[#366bc3]/50 transition cursor-pointer flex flex-col items-center justify-center text-center p-3" data-admin-portfolios-upload-card>
                                <!-- Hidden file input -->
                                <input type="file" name="image_file" accept="image/*" class="hidden" data-admin-portfolios-file-input>
                                
                                <!-- Hidden text input for existing image path -->
                                <input type="hidden" name="image" data-admin-portfolios-field="image">

                                <!-- Placeholder -->
                                <div class="flex flex-col items-center gap-1.5 text-white/35 transition group-hover/uploader:text-white/60" data-admin-portfolios-upload-placeholder>
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-[10px] font-semibold uppercase tracking-wider">{{ __('Upload Cover') }}</span>
                                </div>

                                <!-- Preview Wrapper -->
                                <div class="absolute inset-0 hidden" data-admin-portfolios-preview-wrapper>
                                    <!-- Blurred backdrop fills the box behind portrait covers -->
                                    <img src="" class="absolute inset-0 h-full w-full scale-110 object-cover blur-sm opacity-60" aria-hidden="true" data-admin-portfolios-preview-bg>
                                    <img src="" class="relative h-full w-full object-contain" data-admin-portfolios-preview>
                                    <!-- Hover Action Overlay -->
                                    <div class="absolute inset-0 bg-black/75 opacity-0 group-hover/uploader:opacity-100 transition-opacity flex items-center justify-center gap-3 z-10">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-white bg-white/10 px-2.5 py-1 rounded border border-white/10">{{ __('Change') }}</span>
                                        <button type="button" class="relative z-20 p-2 rounded bg-red-500/80 text-white hover:bg-red-500 transition shadow" data-admin-portfolios-remove-image-file title="{{ __('Delete Image') }}">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-portfolios-error="image_file"></p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-white/8">
                    <button type="submit" class="inline-flex items-center justify-center gap-3 rounded px-8 py-3.5 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-portfolios-save>
                        <svg class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true" data-admin-portfolios-spinner>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span data-admin-portfolios-save-label>{{ __('Save Portfolio Item') }}</span>
                    </button>
                </div>
            </form>
        </section>

        <section class="border border-white/8 bg-[#0d0d13]">
            <div class="flex flex-col gap-4 border-b border-white/8 p-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Portfolio Items List') }}</h2>
                <input placeholder="{{ __('Search portfolio items') }}" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3] sm:max-w-xs" data-admin-portfolios-search>
            </div>

            <!-- Category Filter Tabs -->
            <div class="border-b border-white/8 bg-white/[0.01] px-5 py-3.5 flex flex-wrap gap-2" data-admin-portfolios-tabs>
                <button type="button" class="rounded border px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider transition-all duration-200 border-[#366bc3] bg-[#366bc3]/10 text-white" data-admin-portfolios-tab="">
                    {{ __('All Services') }} ({{ $portfolios->count() }})
                </button>
                @foreach ($services as $service)
                    <button type="button" class="rounded border px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider transition-all duration-200 border-white/10 text-white/42 hover:border-white/25 hover:text-white" data-admin-portfolios-tab="{{ $service->id }}" data-accent="{{ $service->accent }}">
                        {{ $service->title }} ({{ $portfolios->where('service_id', $service->id)->count() }})
                    </button>
                @endforeach
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="border-b border-white/8 text-[11px] uppercase tracking-[0.16em] text-white/35">
                        <tr>
                            <th class="px-5 py-4 w-10"></th>
                            <th class="px-5 py-4 font-semibold w-24">{{ __('Image') }}</th>
                            <th class="px-5 py-4 font-semibold w-1/4">{{ __('Title') }}</th>
                            <th class="px-5 py-4 font-semibold w-1/4">{{ __('Service') }}</th>
                            <th class="px-5 py-4 font-semibold w-1/4">{{ __('Video URL') }}</th>
                            <th class="px-5 py-4 font-semibold w-20">{{ __('Size') }}</th>
                            <th class="px-5 py-4 font-semibold w-20">{{ __('Ratio') }}</th>
                            <th class="px-5 py-4 font-semibold w-20">{{ __('Active') }}</th>
                            <th class="px-5 py-4 font-semibold w-20">{{ __('Order') }}</th>
                            <th class="px-5 py-4 text-right font-semibold w-32">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/7" data-admin-portfolios-table></tbody>
                </table>
            </div>
        </section>

        <div class="vidhya-admin-modal-backdrop fixed inset-0 z-[9000] hidden place-items-center bg-black/65 px-4" data-admin-portfolios-delete-modal data-admin-modal>
            <div class="vidhya-admin-modal w-full max-w-lg space-y-6 rounded-lg p-6" role="dialog" aria-modal="true" aria-labelledby="delete-portfolio-title">
                <div>
                    <h2 id="delete-portfolio-title" class="text-lg font-black uppercase tracking-[0.04em] text-white">{{ __('Delete Portfolio Item?') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-white/45">{{ __('Are you sure you want to delete this portfolio item? It will be immediately removed from the public portfolio page.') }}</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" class="rounded border border-white/10 px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white/58 transition hover:border-white/25 hover:text-white" data-admin-portfolios-delete-cancel data-admin-modal-cancel>{{ __('Cancel') }}</button>

                    <button type="button" class="inline-flex items-center justify-center gap-3 rounded px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #823665, #b4143c, #e60012);" data-admin-portfolios-delete-confirm>
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts::app>
