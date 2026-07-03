<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Director;
use App\Models\Faq;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $directors = Director::query()
            ->select(['first_name', 'last_name', 'role', 'works'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $directorWorksList = $directors->map(function (Director $director): array {
            $works = collect($director->works ?? []);

            return [
                'name' => trim($director->first_name.' '.$director->last_name),
                'role' => $director->role,
                'works_count' => $works->count(),
                'video_count' => $works->filter(fn (array $work) => filled($work['video_url'] ?? null))->count(),
                'still_count' => $works->filter(fn (array $work) => blank($work['video_url'] ?? null) && filled($work['image'] ?? null))->count(),
            ];
        });

        $portfolioStats = Portfolio::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN show_in_portfolio = 1 THEN 1 ELSE 0 END) as published')
            ->selectRaw("SUM(CASE WHEN video_url IS NOT NULL AND video_url != '' THEN 1 ELSE 0 END) as videos")
            ->selectRaw("SUM(CASE WHEN image IS NOT NULL AND image != '' THEN 1 ELSE 0 END) as stills")
            ->first();

        $totalServices = Service::count();
        $serviceIdsWithPortfolio = Portfolio::query()->whereNotNull('service_id')->distinct()->pluck('service_id');

        $recentSources = [
            [Service::class, 'Service', 'title', 'admin.services', '#366bc3'],
            [Portfolio::class, 'Portfolio', 'title', 'admin.portfolios', '#823665'],
            [Director::class, 'Director', null, 'admin.directors', '#e60012'],
            [Faq::class, 'FAQ', 'question', 'admin.faqs', '#a855f7'],
            [Client::class, 'Client', 'name', 'admin.clients', '#22c55e'],
        ];

        $recentUpdates = collect($recentSources)
            ->flatMap(function (array $source) {
                [$model, $type, $titleColumn, $route, $accent] = $source;

                return $model::query()->latest('updated_at')->limit(8)->get()->map(fn ($item) => [
                    'type' => $type,
                    'title' => $titleColumn ? $item->{$titleColumn} : trim($item->first_name.' '.$item->last_name),
                    'updated_at' => $item->updated_at,
                    'route' => route($route),
                    'accent' => $accent,
                ]);
            })
            ->sortByDesc('updated_at')
            ->take(8)
            ->values();

        return view('dashboard', [
            'totalDirectors' => $directors->count(),
            'directorWorksList' => $directorWorksList,
            'totalFaqs' => Faq::count(),
            'totalFaqGroups' => Faq::query()->distinct()->count('category'),
            'totalAdmins' => User::query()->where('role', User::ROLE_ADMIN)->count(),
            'totalServices' => $totalServices,
            'servicesWithoutPortfolio' => Service::query()->whereNotIn('id', $serviceIdsWithPortfolio)->count(),
            'totalPortfolios' => (int) $portfolioStats->total,
            'publishedPortfolios' => (int) $portfolioStats->published,
            'hiddenPortfolios' => (int) $portfolioStats->total - (int) $portfolioStats->published,
            'portfolioVideos' => (int) $portfolioStats->videos,
            'portfolioStills' => (int) $portfolioStats->stills,
            'totalClients' => Client::count(),
            'activeClients' => Client::query()->where('is_active', true)->count(),
            'recentUpdates' => $recentUpdates,
        ]);
    }
}
