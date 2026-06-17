<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Faq;

new #[Title('FAQ')]
    #[Layout('layouts.marketing')]
    class extends Component {
    public $search = '';

    public function resetSearch()
    {
        $this->reset('search');
    }

    public function highlight($text)
    {
        $search = trim($this->search);
        if ($search === '') {
            return e($text);
        }

        $quotedSearch = preg_quote($search, '/');
        return preg_replace('/(' . $quotedSearch . ')/i', '<mark style="background-color: rgba(54, 107, 195, 0.35); color: #ffffff; padding: 0px 4px; border: 1px solid rgba(54, 107, 195, 0.3); border-radius: 4px; font-weight: 600;">$1</mark>', e($text));
    }

    public function render()
    {
        $search = trim($this->search);
        $faqsGrouped = Faq::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('question', 'like', '%' . $search . '%')
                        ->orWhere('answer', 'like', '%' . $search . '%')
                        ->orWhere('category', 'like', '%' . $search . '%')
                        ->orWhere('keywords', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return view('pages.⚡faq', [
            'faqsGrouped' => $faqsGrouped,
        ]);
    }
}; ?>

<main class="bg-[#0a0a0c] text-white">
    <!-- Hero Section -->
    <section class="relative overflow-hidden px-6 pb-24 pt-40 sm:px-10 lg:px-20"
        style="background: radial-gradient(ellipse at 15% 85%, rgba(54,107,195,0.18) 0%, #0a0a0c 60%);">
        <div
            class="pointer-events-none absolute -right-15 -top-15 h-[400px] w-[400px] bg-[radial-gradient(ellipse,rgba(54,107,195,0.13)_0%,transparent_65%)]">
        </div>
        <div class="relative z-10 mx-auto max-w-[1800px]" data-reveal>
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.26em] text-white/35">Got Questions?</p>
            <h1
                class="max-w-none text-[clamp(3rem,6.4vw,5.35rem)] font-black uppercase leading-none tracking-[-0.03em]">
                <span
                    class="bg-linear-to-r from-[#366bc3] via-[#6d55a5] to-[#823665] bg-clip-text text-transparent">Frequently
                    Asked </span><span
                    class="bg-linear-to-r from-[#823665] via-[#b4143c] to-[#e60012] bg-clip-text text-transparent">Questions</span>
            </h1>
            <p class="mt-6 max-w-2xl text-[17px] font-normal leading-[1.8] text-white/48">Everything you need to know
                about our cinematic AI video workflows, services, creative process, and how we deliver premium content
                at scale.</p>
        </div>
    </section>

    <!-- FAQ Accordion Section -->
    <section class="px-6 py-20 sm:px-10 lg:px-20">
        <div class="mx-auto max-w-[1000px] space-y-16">
            <!-- Search Bar -->
            <div class="relative max-w-xl mx-auto" data-reveal>
                <div class="absolute inset-y-0 left-8 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-white/35" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search for answers, topics, keywords..."
                    class="block w-full py-4 rounded-full border border-white/10 bg-white/[0.03] text-base text-white placeholder-white/28 outline-none transition duration-300 focus:border-[#366bc3] focus:ring-2 focus:ring-[#366bc3]/20"
                    style="padding-left: 4rem; padding-right: 4rem;" />
                @if($search)
                    <button type="button" wire:click="resetSearch"
                        class="absolute inset-y-0 right-8 flex items-center text-white/45 hover:text-white transition"
                        title="Clear search">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>

            @php
                $categoryAccents = [
                    'Workflow & Timeline' => '#366bc3',
                    'Quality & Scalability' => '#823665',
                    'Data Security & Brand Identity' => '#e60012',
                ];
                $categoryHoverColors = [
                    'Workflow & Timeline' => 'hover:text-[#366bc3]',
                    'Quality & Scalability' => 'hover:text-[#823665]',
                    'Data Security & Brand Identity' => 'hover:text-[#e60012]',
                ];
                $accentColorsCycle = ['#366bc3', '#823665', '#e60012'];
                $hoverClassesCycle = ['hover:text-[#366bc3]', 'hover:text-[#823665]', 'hover:text-[#e60012]'];
                $categoryIndex = 0;
            @endphp

            @if($faqsGrouped->isEmpty())
                <div class="text-center py-12" data-reveal>
                    <div
                        class="inline-flex items-center justify-center size-16 rounded-full bg-white/[0.03] border border-white/10 mb-4">
                        <svg class="h-8 w-8 text-white/35" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">No results found</h3>
                    <p class="text-sm text-white/45 mb-6 font-normal">We couldn't find any questions matching
                        "{{ $search }}". Try different keywords or reset search.</p>
                    <button type="button" wire:click="resetSearch"
                        class="inline-flex rounded px-6 py-2.5 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110"
                        style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);">
                        Reset Search
                    </button>
                </div>
            @else
                <div class="space-y-16">
                    @foreach ($faqsGrouped as $category => $faqs)
                        @php
                            $accent = $categoryAccents[$category] ?? $accentColorsCycle[$categoryIndex % count($accentColorsCycle)];
                            $hoverColorClass = $categoryHoverColors[$category] ?? $hoverClassesCycle[$categoryIndex % count($hoverClassesCycle)];
                            $delay = $categoryIndex * 100;
                            $categoryIndex++;
                        @endphp

                        <div x-data="{ active: null }" data-reveal style="--reveal-delay: {{ $delay }}ms;">
                            <h2
                                class="mb-8 text-lg sm:text-xl font-black uppercase tracking-wider text-white flex items-center gap-3 border-b border-white/5 pb-4">
                                <span class="h-2 w-2 rounded" style="background-color: {{ $accent }};"></span>
                                {{ __($category) }}
                            </h2>

                            <div class="space-y-4">
                                @foreach ($faqs as $index => $faq)
                                    <div class="home-animated-card border-l-[3px] bg-[#0c0c12] hover:bg-[#0f0f18] transition-all duration-300"
                                        style="border-color: {{ $accent }};" @if($faq->keywords)
                                        data-keywords="{{ $faq->keywords }}" @endif>
                                        <button @click="active = (active === {{ $index }} ? null : {{ $index }})"
                                            class="w-full flex items-center justify-between p-6 text-left focus:outline-none focus-visible:ring-1 focus-visible:ring-white/20"
                                            aria-expanded="active === {{ $index }} ? 'true' : 'false'">
                                            <span
                                                class="text-sm sm:text-base font-black uppercase tracking-wider text-white {{ $hoverColorClass }} transition-colors duration-200">
                                                {!! $this->highlight($faq->question) !!}
                                            </span>
                                            <span
                                                class="ml-4 flex size-8 shrink-0 items-center justify-center rounded border border-white/10 bg-white/[0.03] text-white/50 transition-all duration-300"
                                                :class="active === {{ $index }} ? 'rotate-180 border-white/20 bg-white/10 text-white' : ''">
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </span>
                                        </button>
                                        <div class="grid transition-all duration-300 ease-in-out"
                                            :class="active === {{ $index }} ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                                            <div class="overflow-hidden">
                                                <div
                                                    class="p-6 pt-5 border-t border-white/5 text-[13px] sm:text-sm font-medium leading-relaxed text-white/50">
                                                    {!! $this->highlight($faq->answer) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </section>

    @if ($faqsGrouped->isNotEmpty())
        @php
            $schemaData = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => []
            ];
            foreach ($faqsGrouped as $category => $faqs) {
                foreach ($faqs as $faq) {
                    $schemaData['mainEntity'][] = [
                        '@type' => 'Question',
                        'name' => $faq->question,
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $faq->answer
                        ]
                    ];
                }
            }
        @endphp
        <script type="application/ld+json">
                {!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
            </script>
    @endif
</main>