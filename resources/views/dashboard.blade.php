@php
    use App\Models\Director;
    use App\Models\Faq;
    use App\Models\Portfolio;
    use App\Models\Service;
    use App\Models\User;

    // Fetch total counts
    $totalDirectors = Director::count();
    $directors = Director::all();
    $totalFaqs = Faq::count();
    $totalFaqGroups = Faq::query()->distinct()->count('category');
    $totalAdmins = User::where('role', User::ROLE_ADMIN)->count();
    $totalServices = Service::count();
    $totalPortfolios = Portfolio::count();
    $publishedPortfolios = Portfolio::where('show_in_portfolio', true)->count();
    $hiddenPortfolios = $totalPortfolios - $publishedPortfolios;
    $portfolioVideos = Portfolio::whereNotNull('video_url')->where('video_url', '!=', '')->count();
    $portfolioStills = $totalPortfolios - $portfolioVideos;
    $serviceIdsWithPortfolio = Portfolio::whereNotNull('service_id')->distinct()->pluck('service_id');
    $servicesWithoutPortfolio = Service::whereNotIn('id', $serviceIdsWithPortfolio)->count();

    // Work statistics breakdown
    $totalWorksCount = 0;
    $videoWorksCount = 0;
    $stillWorksCount = 0;

    $directorWorksList = [];

    foreach ($directors as $director) {
        $worksCount = 0;
        $videoCount = 0;
        $stillCount = 0;

        if ($director->works && is_array($director->works)) {
            foreach ($director->works as $work) {
                $totalWorksCount++;
                $worksCount++;
                if (!empty($work['video_url'])) {
                    $videoWorksCount++;
                    $videoCount++;
                } else {
                    $stillWorksCount++;
                    $stillCount++;
                }
            }
        }

        $directorWorksList[] = [
            'name' => $director->first_name . ' ' . $director->last_name,
            'slug' => $director->slug,
            'role' => $director->role,
            'works_count' => $worksCount,
            'video_count' => $videoCount,
            'still_count' => $stillCount,
        ];
    }

    $recentUpdates = collect()
        ->concat(Service::latest('updated_at')->take(4)->get()->map(fn ($item) => [
            'type' => 'Service', 'title' => $item->title, 'updated_at' => $item->updated_at,
            'route' => route('admin.services'), 'accent' => '#366bc3',
        ]))
        ->concat(Portfolio::latest('updated_at')->take(4)->get()->map(fn ($item) => [
            'type' => 'Portfolio', 'title' => $item->title, 'updated_at' => $item->updated_at,
            'route' => route('admin.portfolios'), 'accent' => '#823665',
        ]))
        ->concat(Director::latest('updated_at')->take(4)->get()->map(fn ($item) => [
            'type' => 'Director', 'title' => trim($item->first_name.' '.$item->last_name), 'updated_at' => $item->updated_at,
            'route' => route('admin.directors'), 'accent' => '#e60012',
        ]))
        ->concat(Faq::latest('updated_at')->take(4)->get()->map(fn ($item) => [
            'type' => 'FAQ', 'title' => $item->question, 'updated_at' => $item->updated_at,
            'route' => route('admin.faqs'), 'accent' => '#a855f7',
        ]))
        ->sortByDesc('updated_at')
        ->take(8)
        ->values();

@endphp

<x-layouts::app :title="__('Dashboard')">
    <div class="min-h-full bg-[#0a0a0c] text-white">
        <!-- Ambient Background Light -->
        <div class="pointer-events-none fixed right-[-120px] top-[-120px] h-[420px] w-[420px] bg-[radial-gradient(ellipse,rgba(230,0,18,0.12)_0%,transparent_65%)]"></div>

        <div class="relative z-10 space-y-6">
            <!-- Header section -->
            <section class="border border-[#366bc3]/18 bg-[#0f0f18] p-5 lg:p-6">
                <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.26em] text-white/35">{{ __('Admin') }}</p>
                <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
                    <div>
                        <h1 class="bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-[clamp(2.25rem,5vw,3.5rem)] font-black uppercase leading-none tracking-[-0.03em] text-transparent">{{ __('Studio Control') }}</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-white/45">{{ __('A focused backend workspace for managing Vidhya Studio content.') }}</p>
                    </div>
                    <a href="{{ route('home') }}" class="inline-flex rounded px-7 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:brightness-110" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" wire:navigate.hover>
                        {{ __('View Site') }}
                    </a>
                </div>
            </section>

            <!-- Metrics Statistics Cards Grid -->
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <!-- AI Directors -->
                <article class="border-t-2 bg-[#0d0d13] px-6 py-6 border-[#366bc3] transition-all hover:bg-white/[0.015]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#366bc3]">{{ __('AI Directors') }}</p>
                    <p class="mt-3 text-3xl font-black uppercase tracking-[-0.02em] text-white">{{ $totalDirectors }}</p>
                    <p class="mt-2 text-xs text-white/35">{{ __('Registered Profiles') }}</p>
                </article>

                <!-- Portfolio -->
                <article class="border-t-2 bg-[#0d0d13] px-6 py-6 border-[#823665] transition-all hover:bg-white/[0.015]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#823665]">{{ __('Portfolio') }}</p>
                    <p class="mt-3 text-3xl font-black uppercase tracking-[-0.02em] text-white">{{ $totalPortfolios }}</p>
                    <p class="mt-2 text-xs text-white/35">
                        <span class="font-semibold text-emerald-400">{{ $publishedPortfolios }}</span> {{ __('Published') }} · <span class="font-semibold text-white/60">{{ $hiddenPortfolios }}</span> {{ __('Hidden') }}
                    </p>
                </article>

                <!-- Services -->
                <article class="border-t-2 border-[#6d55a5] bg-[#0d0d13] px-6 py-6 transition-all hover:bg-white/[0.015]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#8b7bd1]">{{ __('Services') }}</p>
                    <p class="mt-3 text-3xl font-black uppercase tracking-[-0.02em] text-white">{{ $totalServices }}</p>
                    <p class="mt-2 text-xs text-white/35">{{ $servicesWithoutPortfolio }} {{ __('without portfolio work') }}</p>
                </article>

                <!-- FAQ Database -->
                <article class="border-t-2 bg-[#0d0d13] px-6 py-6 border-[#e60012] transition-all hover:bg-white/[0.015]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#e60012]">{{ __('FAQ Database') }}</p>
                    <p class="mt-3 text-3xl font-black uppercase tracking-[-0.02em] text-white">{{ $totalFaqs }}</p>
                    <p class="mt-2 text-xs text-white/35">{{ $totalFaqGroups }} {{ __('Groups') }}</p>
                </article>

                <!-- Active Administrators -->
                <article class="border-t-2 bg-[#0d0d13] px-6 py-6 border-white/20 transition-all hover:bg-white/[0.015]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-white/50">{{ __('Administrators') }}</p>
                    <p class="mt-3 text-3xl font-black uppercase tracking-[-0.02em] text-white">{{ $totalAdmins }}</p>
                    <p class="mt-2 text-xs text-white/35">{{ __('Authorized Accounts') }}</p>
                </article>
            </section>

            <!-- Detailed Stats Split View -->
            <section class="grid gap-6 lg:grid-cols-3">
                <!-- Left Column: Director Roster list (Span 2) -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="border border-white/8 bg-[#0d0d13] p-5 sm:p-6">
                        <div class="mb-4 flex items-center justify-between gap-4 border-b border-white/5 pb-4">
                            <div>
                                <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Recent Updates') }}</h2>
                                <p class="mt-1 text-xs text-white/35">{{ __('Latest content changes across the studio.') }}</p>
                            </div>
                        </div>

                        @if ($recentUpdates->isEmpty())
                            <p class="py-6 text-center text-sm text-white/35">{{ __('No content updates yet.') }}</p>
                        @else
                            <div class="divide-y divide-white/5">
                                @foreach ($recentUpdates as $update)
                                    <a href="{{ $update['route'] }}" class="grid gap-2 py-3 transition hover:bg-white/[0.02] sm:grid-cols-[110px_1fr_auto] sm:items-center sm:px-2" wire:navigate.hover>
                                        <span class="text-[10px] font-bold uppercase tracking-[0.14em]" style="color: {{ $update['accent'] }}">{{ $update['type'] }}</span>
                                        <span class="truncate text-sm font-medium text-white/75">{{ $update['title'] }}</span>
                                        <span class="text-[11px] text-white/30">{{ $update['updated_at']?->diffForHumans() }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="border border-white/8 bg-[#0d0d13] p-5 sm:p-6">
                        <div class="flex items-center justify-between gap-4 border-b border-white/5 pb-4 mb-4">
                            <h2 class="text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Director Roster Status') }}</h2>
                            <a href="{{ route('admin.directors') }}" class="rounded border border-white/10 px-3.5 py-2 text-[10px] font-semibold uppercase tracking-wider text-white/58 transition hover:border-[#366bc3] hover:text-[#366bc3]">{{ __('Manage Directors') }}</a>
                        </div>

                        @if (empty($directorWorksList))
                            <p class="text-sm text-white/35 py-4 text-center">{{ __('No directors registered yet.') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs text-white/65">
                                    <thead class="border-b border-white/5 text-[9px] uppercase tracking-wider text-white/35">
                                        <tr>
                                            <th class="pb-2 font-semibold">{{ __('Name') }}</th>
                                            <th class="pb-2 font-semibold">{{ __('Tagline') }}</th>
                                            <th class="pb-2 text-center font-semibold">{{ __('Videos') }}</th>
                                            <th class="pb-2 text-center font-semibold">{{ __('Stills') }}</th>
                                            <th class="pb-2 text-right font-semibold">{{ __('Total Works') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        @foreach ($directorWorksList as $dirItem)
                                            <tr class="hover:bg-white/[0.02] transition">
                                                <td class="py-3 font-semibold text-white">{{ $dirItem['name'] }}</td>
                                                <td class="py-3 text-white/45 max-w-[200px] truncate" title="{{ $dirItem['role'] }}">{{ $dirItem['role'] }}</td>
                                                <td class="py-3 text-center font-mono">{{ $dirItem['video_count'] }}</td>
                                                <td class="py-3 text-center font-mono">{{ $dirItem['still_count'] }}</td>
                                                <td class="py-3 text-right font-mono font-bold text-white">{{ $dirItem['works_count'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: System Status & Quick Actions -->
                <div class="space-y-6">
                    <article class="border border-white/8 bg-[#0d0d13] p-5 sm:p-6">
                        <h2 class="mb-4 border-b border-white/5 pb-3 text-sm font-black uppercase tracking-[0.05em] text-white">{{ __('Publishing Snapshot') }}</h2>
                        <div class="grid grid-cols-2 gap-3 text-center">
                            <div class="border border-white/7 bg-black/15 px-3 py-4"><p class="text-2xl font-black text-white">{{ $portfolioVideos }}</p><p class="mt-1 text-[10px] uppercase tracking-wider text-white/35">{{ __('Videos') }}</p></div>
                            <div class="border border-white/7 bg-black/15 px-3 py-4"><p class="text-2xl font-black text-white">{{ $portfolioStills }}</p><p class="mt-1 text-[10px] uppercase tracking-wider text-white/35">{{ __('Stills') }}</p></div>
                        </div>
                    </article>


                    <!-- Quick Actions -->
                    <article class="border border-white/8 bg-[#0d0d13] p-5 sm:p-6">
                        <h2 class="mb-4 text-sm font-black uppercase tracking-[0.05em] text-white border-b border-white/5 pb-3">{{ __('Quick Shortcuts') }}</h2>
                        <div class="grid gap-2">
                            <a href="{{ route('admin.services') }}" class="flex items-center justify-between rounded border border-white/8 px-4 py-2.5 text-xs text-white/58 transition hover:border-white/20 hover:bg-white/[0.04] hover:text-white" wire:navigate.hover>
                                <span>{{ __('Manage Services') }}</span><span>→</span>
                            </a>
                            <a href="{{ route('admin.portfolios') }}" class="flex items-center justify-between rounded border border-white/8 px-4 py-2.5 text-xs text-white/58 transition hover:border-white/20 hover:bg-white/[0.04] hover:text-white" wire:navigate.hover>
                                <span>{{ __('Manage Portfolio') }}</span><span>→</span>
                            </a>
                            <a href="{{ route('admin.directors') }}" class="flex items-center justify-between rounded border border-white/8 px-4 py-2.5 text-xs text-white/58 transition hover:border-white/20 hover:bg-white/[0.04] hover:text-white" wire:navigate.hover>
                                <span>{{ __('Manage Directors Roster') }}</span>
                                <span>→</span>
                            </a>
                            <a href="{{ route('admin.faqs') }}" class="flex items-center justify-between rounded border border-white/8 px-4 py-2.5 text-xs text-white/58 transition hover:border-white/20 hover:bg-white/[0.04] hover:text-white" wire:navigate.hover>
                                <span>{{ __('Manage FAQs & Support') }}</span>
                                <span>→</span>
                            </a>
                            <a href="{{ route('admin.users') }}" class="flex items-center justify-between rounded border border-white/8 px-4 py-2.5 text-xs text-white/58 transition hover:border-white/20 hover:bg-white/[0.04] hover:text-white" wire:navigate.hover>
                                <span>{{ __('Manage Admin Accounts') }}</span>
                                <span>→</span>
                            </a>
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </div>
</x-layouts::app>
