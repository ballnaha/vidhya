@php
    use App\Models\Director;

    $directors = Director::query()
        ->latest()
        ->limit(50)
        ->get()
        ->map(fn (Director $director) => [
            'id' => $director->id,
            'first_name' => $director->first_name,
            'last_name' => $director->last_name,
            'slug' => $director->slug,
            'eyebrow' => $director->eyebrow,
            'role' => $director->role,
            'bio_title_white' => $director->bio_title_white,
            'bio_title_gradient' => $director->bio_title_gradient,
            'bio_image' => $director->bio_image,
            'bio_alt' => $director->bio_alt,
            'works_eyebrow' => $director->works_eyebrow,
            'works_title_white' => $director->works_title_white,
            'works_title_muted' => $director->works_title_muted,
            'bio_raw' => implode("\n\n", $director->bio ?? []),
            'works_raw' => json_encode($director->works ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'stat_1_value' => $director->stats[0]['value'] ?? '',
            'stat_1_suffix' => $director->stats[0]['suffix'] ?? '',
            'stat_1_label' => $director->stats[0]['label'] ?? '',
            'stat_2_value' => $director->stats[1]['value'] ?? '',
            'stat_2_suffix' => $director->stats[1]['suffix'] ?? '',
            'stat_2_label' => $director->stats[1]['label'] ?? '',
            'stat_3_value' => $director->stats[2]['value'] ?? '',
            'stat_3_suffix' => $director->stats[2]['suffix'] ?? '',
            'stat_3_label' => $director->stats[2]['label'] ?? '',
            'created_at' => $director->created_at?->format('M j, Y'),
        ])
        ->values();
@endphp

<x-layouts::app :title="__('Directors')">
<div
    class="min-h-full bg-[#0a0a0c] text-white"
    data-admin-directors
    data-index-url="{{ route('admin.directors.index') }}"
    data-check-slug-url="{{ route('admin.directors.check-slug') }}"
    data-store-url="{{ route('admin.directors.store') }}"
    data-update-url-template="{{ url('admin/directors/__DIRECTOR__') }}"
    data-delete-url-template="{{ url('admin/directors/__DIRECTOR__') }}"
>
    <script type="application/json" data-admin-directors-initial>@json($directors)</script>

    <div class="space-y-6">
        <section class="border border-[#366bc3]/18 bg-[#0f0f18] p-5 lg:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.26em] text-white/35">{{ __('Admin') }}</p>
                    <h1 class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-[clamp(2.25rem,5vw,3.5rem)] font-black uppercase leading-none tracking-[-0.03em] text-transparent">{{ __('Directors') }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-white/45">{{ __('Manage and customize directors profiles, stats, biography, and portfolio video links.') }}</p>
                </div>

                <button type="button" class="inline-flex rounded px-7 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-directors-create>
                    {{ __('New Director') }}
                </button>
            </div>
        </section>

        <section class="hidden border border-white/8 bg-[#0d0d13] p-6" data-admin-directors-form-shell>
            <div class="mb-5 flex items-center justify-between gap-4 border-b border-white/8 pb-3">
                <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white" data-admin-directors-form-title>{{ __('Create Director') }}</h2>
                <button type="button" class="text-xs font-medium uppercase tracking-[0.12em] text-white/38 transition hover:text-white" data-admin-directors-cancel>{{ __('Cancel') }}</button>
            </div>

            <form class="space-y-6" data-admin-directors-form enctype="multipart/form-data">
                <input type="hidden" name="id" data-admin-directors-id>

                <!-- Core Details -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#366bc3]">{{ __('Core Profile Details') }}</h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('First Name') }}</label>
                            <input name="first_name" placeholder="Sunil" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-directors-field="first_name">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="first_name"></p>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Last Name') }}</label>
                            <input name="last_name" placeholder="Thomas" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-directors-field="last_name">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="last_name"></p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Slug (Unique Tab ID)') }}</label>
                            <button type="button" class="inline-flex items-center gap-1 rounded-full bg-[#366bc3]/10 border border-[#366bc3]/20 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-[#366bc3] hover:bg-[#366bc3]/20 hover:text-white transition" data-admin-directors-fill-general>
                                {{ __('General Studio Profile') }}
                            </button>
                        </div>
                        <input name="slug" placeholder="sunil-thomas" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-directors-field="slug">
                        <p class="mt-1.5 text-[11px] text-white/35 leading-normal">
                            {{ __('Only lowercase English letters, numbers, dashes, and underscores are allowed (Thai characters are not supported).') }}
                            <br><span class="text-[#366bc3] font-bold">{{ __('Note:') }}</span> {{ __('Use slug "general" (First Name: Vidhya, Last Name: Studio) for uploading general/studio works not belonging to any specific AI Director. This profile will be automatically hidden from the AI Director Roster page, and its works will show in the portfolio under "Vidhya Studio".') }}
                        </p>
                        <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="slug"></p>
                    </div>


                    <div class="grid gap-4 md:grid-cols-2" data-admin-directors-not-general>
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Eyebrow Subtitle') }}</label>
                            <input name="eyebrow" placeholder="AI Director · Vidhya Studio" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-directors-field="eyebrow">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="eyebrow"></p>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Role / Tagline') }}</label>
                            <input name="role" placeholder="Commercial Director·AI Filmmaker·300+ Brands" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-directors-field="role">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="role"></p>
                        </div>
                    </div>
                </div>

                <!-- Statistics Blocks -->
                <div class="space-y-4 pt-4 border-t border-white/8" data-admin-directors-not-general>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#366bc3]">{{ __('Statistics (3 blocks)') }}</h3>
                    
                    <!-- Stat 1 -->
                    <div class="grid gap-4 md:grid-cols-3 bg-white/[0.01] p-3 border border-white/5 rounded">
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Stat 1 Value') }}</label>
                            <input name="stat_1_value" placeholder="300" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="stat_1_value">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="stat_1_value"></p>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Stat 1 Suffix') }}</label>
                            <input name="stat_1_suffix" placeholder="+" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="stat_1_suffix">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="stat_1_suffix"></p>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Stat 1 Label') }}</label>
                            <input name="stat_1_label" placeholder="Commercials Directed" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="stat_1_label">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="stat_1_label"></p>
                        </div>
                    </div>

                    <!-- Stat 2 -->
                    <div class="grid gap-4 md:grid-cols-3 bg-white/[0.01] p-3 border border-white/5 rounded">
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Stat 2 Value') }}</label>
                            <input name="stat_2_value" placeholder="25" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="stat_2_value">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="stat_2_value"></p>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Stat 2 Suffix') }}</label>
                            <input name="stat_2_suffix" placeholder="yrs" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="stat_2_suffix">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="stat_2_suffix"></p>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Stat 2 Label') }}</label>
                            <input name="stat_2_label" placeholder="Industry Experience" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="stat_2_label">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="stat_2_label"></p>
                        </div>
                    </div>

                    <!-- Stat 3 -->
                    <div class="grid gap-4 md:grid-cols-3 bg-white/[0.01] p-3 border border-white/5 rounded">
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Stat 3 Value') }}</label>
                            <input name="stat_3_value" placeholder="4" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="stat_3_value">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="stat_3_value"></p>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Stat 3 Suffix') }}</label>
                            <input name="stat_3_suffix" placeholder="" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="stat_3_suffix">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="stat_3_suffix"></p>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Stat 3 Label') }}</label>
                            <input name="stat_3_label" placeholder="Global Markets" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="stat_3_label">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="stat_3_label"></p>
                        </div>
                    </div>
                </div>

                <!-- Biography Details (Section 2) -->
                <div class="space-y-4 pt-4 border-t border-white/8" data-admin-directors-not-general>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#366bc3]">{{ __('Biography (Section 2)') }}</h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Bio Title (White Text)') }}</label>
                            <input name="bio_title_white" placeholder="Two Decades." class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="bio_title_white">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="bio_title_white"></p>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Bio Title (Gradient Text)') }}</label>
                            <input name="bio_title_gradient" placeholder="One Vision." class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="bio_title_gradient">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="bio_title_gradient"></p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Bio Image (Upload file will be auto-resized to 1920x1080)') }}</label>
                            <input type="file" name="bio_image_file" accept="image/*" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-2 text-sm text-white outline-none transition focus:border-[#366bc3] file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-white/10 file:text-white hover:file:bg-white/20" data-admin-directors-field="bio_image_file">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="bio_image_file"></p>
                            <input type="hidden" name="bio_image" data-admin-directors-bio-image>
                            <div class="mt-3 text-xs text-white/35 flex flex-col gap-2 border border-white/5 bg-white/[0.01] p-3 rounded" data-admin-directors-current-image-wrapper>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="font-semibold uppercase tracking-wider text-white/45 text-[10px]">{{ __('Current Biography Image:') }}</span>
                                    <button type="button" class="inline-flex items-center gap-1 rounded bg-[#e60012]/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-[#e60012] border border-[#e60012]/20 hover:bg-[#e60012]/20 hover:text-white transition" data-admin-directors-delete-image>
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                                <div class="relative mt-1 aspect-video w-full max-w-[280px] overflow-hidden border border-white/10 bg-black rounded">
                                    <img src="" alt="" class="h-full w-full object-cover" data-admin-directors-current-image-preview>
                                </div>
                                <a href="#" target="_blank" data-admin-directors-current-image class="text-[#366bc3] underline hover:text-white truncate max-w-[280px] mt-1 block font-mono text-[10px]"></a>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Bio Image Alt Text') }}</label>
                            <input name="bio_alt" placeholder="Sunil Thomas directing with a headset" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="bio_alt">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="bio_alt"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Biography Paragraphs (One paragraph per line. Keep lines separate.)') }}</label>
                        <textarea name="bio_raw" rows="6" placeholder="Type bio paragraphs here..." class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-directors-field="bio_raw"></textarea>
                        <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="bio_raw"></p>
                    </div>
                </div>

                <!-- Works Details (Section 3) -->
                <div class="space-y-4 pt-4 border-t border-white/8">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#366bc3]">{{ __('Works Portfolio (Section 3)') }}</h3>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Works Section Eyebrow') }}</label>
                            <input name="works_eyebrow" placeholder="Core Expertise" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="works_eyebrow">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="works_eyebrow"></p>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Works Section Title (White)') }}</label>
                            <input name="works_title_white" placeholder="What He" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="works_title_white">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="works_title_white"></p>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Works Section Title (Muted)') }}</label>
                            <input name="works_title_muted" placeholder="Brings" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-field="works_title_muted">
                            <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="works_title_muted"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Works Portfolio') }}</label>
                        <input type="hidden" name="works_raw" data-admin-directors-works-raw-input>
                        
                        <div class="space-y-4" data-admin-directors-works-container>
                            <!-- Repeater rows populated via JS -->
                        </div>
                        
                        <div class="mt-4">
                            <button type="button" class="inline-flex items-center gap-2 rounded border border-white/10 bg-white/[0.03] px-5 py-3 text-xs font-semibold uppercase tracking-wider text-white transition hover:bg-white/[0.08]" data-admin-directors-add-work>
                                <svg class="size-4 text-[#366bc3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                {{ __('Add Work (Video or Image)') }}
                            </button>
                        </div>
                        
                        <p class="mt-2 hidden text-xs text-red-400" data-admin-directors-error="works_raw"></p>
                    </div>
                </div>

                <template data-admin-directors-work-row-template>
                    <div class="group relative flex gap-4 rounded border border-white/5 bg-white/[0.015] p-5 transition hover:border-white/10 items-start" data-admin-directors-work-row>
                        <!-- Left Handle & Index -->
                        <div class="flex items-center gap-2 h-10 sm:h-11 shrink-0">
                            <div class="cursor-grab text-white/20 hover:text-white/60 transition hidden sm:block" title="Drag to reorder" data-admin-directors-work-drag-handle>
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 9h8M8 15h8" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-white/25 group-hover:text-[#366bc3] transition" data-admin-directors-work-number>#1</span>
                        </div>

                        <!-- Main Form Content -->
                        <div class="flex-1 space-y-4">
                            <!-- Row 1: Main Fields -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block mb-1.5 text-[10px] font-semibold text-white/35 uppercase tracking-wider">{{ __('Title') }}</label>
                                    <input type="text" placeholder="Title (e.g. Nike - Rise)" class="w-full rounded border border-white/10 bg-white/[0.04] px-3.5 py-2.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-directors-work-field="title">
                                </div>
                                <div>
                                    <label class="block mb-1.5 text-[10px] font-semibold text-white/35 uppercase tracking-wider">{{ __('YouTube / Vimeo Link (optional)') }}</label>
                                    <input type="text" placeholder="https://youtube.com/..." class="w-full rounded border border-white/10 bg-white/[0.04] px-3.5 py-2.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-directors-work-field="video_url">
                                </div>
                                <div>
                                    <label class="block mb-1.5 text-[10px] font-semibold text-white/35 uppercase tracking-wider">{{ __('Custom Thumbnail / Photo') }}</label>
                                    <input type="file" accept="image/*" class="w-full rounded border border-white/10 bg-white/[0.04] px-2 py-1.5 text-xs text-white file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-white/10 file:text-white hover:file:bg-white/20" data-admin-directors-work-file-input>
                                    
                                    <div class="flex items-center gap-2.5 mt-2.5 hidden" data-admin-directors-work-preview-wrapper>
                                        <img src="" class="h-8 w-12 object-cover border border-white/10 rounded" data-admin-directors-work-preview>
                                        <div class="flex flex-col min-w-0 leading-tight">
                                            <span class="text-[9px] text-white/35 truncate max-w-[120px] font-mono" data-admin-directors-work-path-label></span>
                                            <button type="button" class="text-red-500 hover:text-red-400 transition mt-1 w-fit" data-admin-directors-work-remove-image-file title="{{ __('Delete Image') }}">
                                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Settings & Metadata -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-xl">
                                <div>
                                    <label class="block mb-1.5 text-[10px] font-semibold text-white/35 uppercase tracking-wider">{{ __('Portfolio Visibility') }}</label>
                                    <select class="w-full rounded border border-white/10 bg-[#0d0d13] px-3 py-2.5 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-work-field="show_in_portfolio">
                                        <option value="1">{{ __('Show in Portfolio') }}</option>
                                        <option value="0">{{ __('Hide from Portfolio (Profile only)') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-1.5 text-[10px] font-semibold text-white/35 uppercase tracking-wider">{{ __('Display Size (Director Page Grid)') }}</label>
                                    <select class="w-full rounded border border-white/10 bg-[#0d0d13] px-3 py-2.5 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-directors-work-field="span">
                                        <option value="md:col-span-2">{{ __('1/3 Width') }}</option>
                                        <option value="md:col-span-3">{{ __('1/2 Width') }}</option>
                                        <option value="md:col-span-4">{{ __('2/3 Width') }}</option>
                                        <option value="md:col-span-6">{{ __('Full Width') }}</option>
                                        <option value="md:col-span-2 md:col-start-2">{{ __('1/3 Centered') }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Image path is stored as hidden field -->
                            <input type="hidden" data-admin-directors-work-field="image">
                        </div>

                        <!-- Right Actions -->
                        <div class="shrink-0 h-10 sm:h-11 flex items-center">
                            <button type="button" class="grid size-10 place-items-center rounded border border-white/10 bg-white/[0.02] text-white/45 hover:border-red-500/30 hover:bg-red-500/10 hover:text-red-400 transition" data-admin-directors-remove-work title="{{ __('Remove work') }}">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <div class="flex justify-end pt-4 border-t border-white/8">
                    <button type="submit" class="inline-flex items-center justify-center gap-3 rounded px-8 py-4 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-directors-save>
                        <svg class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true" data-admin-directors-spinner>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span data-admin-directors-save-label>{{ __('Save Director') }}</span>
                    </button>
                </div>
            </form>
        </section>

        <section class="border border-white/8 bg-[#0d0d13]">
            <div class="flex flex-col gap-4 border-b border-white/8 p-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Director List') }}</h2>
                <input placeholder="{{ __('Search directors') }}" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3] sm:max-w-xs" data-admin-directors-search>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="border-b border-white/8 text-[11px] uppercase tracking-[0.16em] text-white/35">
                        <tr>
                            <th class="px-5 py-4 font-semibold">{{ __('Name') }}</th>
                            <th class="px-5 py-4 font-semibold">{{ __('Slug') }}</th>
                            <th class="px-5 py-4 font-semibold">{{ __('Role') }}</th>
                            <th class="px-5 py-4 font-semibold">{{ __('Created') }}</th>
                            <th class="px-5 py-4 text-right font-semibold">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/7" data-admin-directors-table></tbody>
                </table>
            </div>
        </section>

        <div class="vidhya-admin-modal-backdrop fixed inset-0 z-[9000] hidden place-items-center bg-black/65 px-4" data-admin-directors-delete-modal data-admin-modal>
            <div class="vidhya-admin-modal w-full max-w-lg space-y-6 rounded-lg p-6" role="dialog" aria-modal="true" aria-labelledby="delete-director-title">
                <div>
                    <h2 id="delete-director-title" class="text-lg font-black uppercase tracking-[0.04em] text-white">{{ __('Delete director?') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-white/45">{{ __('This action cannot be undone. The selected director will be permanently removed from the database.') }}</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" class="rounded border border-white/10 px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white/58 transition hover:border-white/25 hover:text-white" data-admin-directors-delete-cancel data-admin-modal-cancel>{{ __('Cancel') }}</button>

                    <button type="button" class="inline-flex items-center justify-center gap-3 rounded px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #823665, #b4143c, #e60012);" data-admin-directors-delete-confirm>
                        <svg class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true" data-admin-directors-delete-spinner>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span data-admin-directors-delete-label>{{ __('Delete') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="vidhya-admin-modal-backdrop fixed inset-0 z-[9000] hidden place-items-center bg-black/65 px-4" data-admin-directors-delete-image-modal data-admin-modal>
            <div class="vidhya-admin-modal w-full max-w-lg space-y-6 rounded-lg p-6" role="dialog" aria-modal="true" aria-labelledby="delete-image-title">
                <div>
                    <h2 id="delete-image-title" class="text-lg font-black uppercase tracking-[0.04em] text-white">{{ __('Delete biography image?') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-white/45">{{ __('Are you sure you want to remove the biography image for this director? This will permanently delete the image file from the server when saved.') }}</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" class="rounded border border-white/10 px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white/58 transition hover:border-white/25 hover:text-white" data-admin-directors-delete-image-cancel data-admin-modal-cancel>{{ __('Cancel') }}</button>

                    <button type="button" class="inline-flex items-center justify-center gap-3 rounded px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #823665, #b4143c, #e60012);" data-admin-directors-delete-image-confirm>
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="vidhya-admin-modal-backdrop fixed inset-0 z-[9000] hidden place-items-center bg-black/65 px-4" data-admin-directors-delete-work-modal data-admin-modal>
            <div class="vidhya-admin-modal w-full max-w-lg space-y-6 rounded-lg p-6" role="dialog" aria-modal="true" aria-labelledby="delete-work-title">
                <div>
                    <h2 id="delete-work-title" class="text-lg font-black uppercase tracking-[0.04em] text-white">{{ __('Delete video work?') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-white/45">{{ __('Are you sure you want to remove this video work from the list? This will remove the work when saved.') }}</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" class="rounded border border-white/10 px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white/58 transition hover:border-white/25 hover:text-white" data-admin-directors-delete-work-cancel data-admin-modal-cancel>{{ __('Cancel') }}</button>

                    <button type="button" class="inline-flex items-center justify-center gap-3 rounded px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #823665, #b4143c, #e60012);" data-admin-directors-delete-work-confirm>
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts::app>
