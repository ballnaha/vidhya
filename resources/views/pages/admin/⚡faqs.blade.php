@php
    use App\Models\Faq;

    $faqs = Faq::query()
        ->orderBy('category')
        ->orderBy('sort_order')
        ->get()
        ->map(fn (Faq $faq) => [
            'id' => $faq->id,
            'category' => $faq->category,
            'question' => $faq->question,
            'answer' => $faq->answer,
            'keywords' => $faq->keywords,
            'sort_order' => $faq->sort_order,
            'created_at' => $faq->created_at?->format('M j, Y'),
        ])
        ->values();
@endphp

<x-layouts::app :title="__('FAQ')">
<div
    class="min-h-full bg-[#0a0a0c] text-white"
    data-admin-faqs
    data-index-url="{{ route('admin.faqs.index') }}"
    data-store-url="{{ route('admin.faqs.store') }}"
    data-update-url-template="{{ url('admin/faqs/__FAQ__') }}"
    data-delete-url-template="{{ url('admin/faqs/__FAQ__') }}"
>
    <script type="application/json" data-admin-faqs-initial>@json($faqs)</script>

    <div class="space-y-6">
        <section class="border border-[#366bc3]/18 bg-[#0f0f18] p-5 lg:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.26em] text-white/35">{{ __('Admin') }}</p>
                    <h1 class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-[clamp(2.25rem,5vw,3.5rem)] font-black uppercase leading-none tracking-[-0.03em] text-transparent">{{ __('FAQ') }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-white/45">{{ __('Manage the frequently asked questions displayed on the marketing website.') }}</p>
                </div>

                <button type="button" class="inline-flex rounded px-7 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-faqs-create>
                    {{ __('New FAQ') }}
                </button>
            </div>
        </section>

        <section class="hidden border border-white/8 bg-[#0d0d13] p-6" data-admin-faqs-form-shell>
            <div class="mb-5 flex items-center justify-between gap-4 border-b border-white/8 pb-3">
                <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white" data-admin-faqs-form-title>{{ __('Create FAQ') }}</h2>
                <button type="button" class="text-xs font-medium uppercase tracking-[0.12em] text-white/38 transition hover:text-white" data-admin-faqs-cancel>{{ __('Cancel') }}</button>
            </div>

            <form class="space-y-4" data-admin-faqs-form>
                <input type="hidden" name="id" data-admin-faqs-id>

                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Category') }}</label>
                        <select name="category_select" class="w-full rounded border border-white/10 bg-[#111118] px-4 py-3.5 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-faqs-category-select>
                            <option value="Workflow & Timeline">{{ __('Workflow & Timeline') }}</option>
                            <option value="Quality & Scalability">{{ __('Quality & Scalability') }}</option>
                            <option value="Data Security & Brand Identity">{{ __('Data Security & Brand Identity') }}</option>
                            <option value="__NEW__">{{ __('+ Add Custom Category...') }}</option>
                        </select>
                        <input name="category" type="text" placeholder="{{ __('Enter custom category...') }}" class="mt-2 w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3] hidden" data-admin-faqs-field="category">
                        <p class="mt-2 hidden text-xs text-red-400" data-admin-faqs-error="category"></p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Question') }}</label>
                        <input name="question" placeholder="{{ __('e.g. How much faster is AI video production?') }}" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-faqs-field="question">
                        <p class="mt-2 hidden text-xs text-red-400" data-admin-faqs-error="question"></p>
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Answer') }}</label>
                    <textarea name="answer" rows="5" placeholder="{{ __('Type answer content here...') }}" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-faqs-field="answer"></textarea>
                    <p class="mt-2 hidden text-xs text-red-400" data-admin-faqs-error="answer"></p>
                </div>

                <div>
                    <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Search Intent Keywords (Inferred)') }}</label>
                    <input name="keywords" placeholder="{{ __('e.g. fast AI video creation, Vidhya Studio workflow efficiency (comma separated)') }}" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]" data-admin-faqs-field="keywords">
                    <p class="mt-2 hidden text-xs text-red-400" data-admin-faqs-error="keywords"></p>
                </div>

                <div>
                    <label class="block mb-2 text-xs font-semibold text-white/45 uppercase tracking-wider">{{ __('Sort Order') }}</label>
                    <input name="sort_order" type="number" min="0" placeholder="10" class="w-24 rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition focus:border-[#366bc3]" data-admin-faqs-field="sort_order">
                    <p class="mt-2 hidden text-xs text-red-400" data-admin-faqs-error="sort_order"></p>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-3 rounded px-8 py-3.5 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-admin-faqs-save>
                        <svg class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true" data-admin-faqs-spinner>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span data-admin-faqs-save-label>{{ __('Save FAQ') }}</span>
                    </button>
                </div>
            </form>
        </section>

        <section class="border border-white/8 bg-[#0d0d13]">
            <div class="flex flex-col gap-4 border-b border-white/8 p-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('FAQ List') }}</h2>
                <input placeholder="{{ __('Search FAQs') }}" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3] sm:max-w-xs" data-admin-faqs-search>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="border-b border-white/8 text-[11px] uppercase tracking-[0.16em] text-white/35">
                        <tr>
                            <th class="px-5 py-4 font-semibold w-1/4">{{ __('Category') }}</th>
                            <th class="px-5 py-4 font-semibold w-1/3">{{ __('Question') }}</th>
                            <th class="px-5 py-4 font-semibold w-1/3">{{ __('Answer') }}</th>
                            <th class="px-5 py-4 font-semibold w-20">{{ __('Order') }}</th>
                            <th class="px-5 py-4 text-right font-semibold w-40">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/7" data-admin-faqs-table></tbody>
                </table>
            </div>
        </section>

        <div class="vidhya-admin-modal-backdrop fixed inset-0 z-[9000] hidden place-items-center bg-black/65 px-4" data-admin-faqs-delete-modal data-admin-modal>
            <div class="vidhya-admin-modal w-full max-w-lg space-y-6 rounded-lg p-6" role="dialog" aria-modal="true" aria-labelledby="delete-faq-title">
                <div>
                    <h2 id="delete-faq-title" class="text-lg font-black uppercase tracking-[0.04em] text-white">{{ __('Delete FAQ?') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-white/45">{{ __('Are you sure you want to delete this FAQ? It will be immediately removed from the public page.') }}</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" class="rounded border border-white/10 px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white/58 transition hover:border-white/25 hover:text-white" data-admin-faqs-delete-cancel data-admin-modal-cancel>{{ __('Cancel') }}</button>

                    <button type="button" class="inline-flex items-center justify-center gap-3 rounded px-5 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-70" style="background: linear-gradient(90deg, #823665, #b4143c, #e60012);" data-admin-faqs-delete-confirm>
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts::app>
