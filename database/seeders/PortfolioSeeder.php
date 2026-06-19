<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $portfolios = [
            [
                'title' => 'AI POCs & Previs',
                'video_url' => 'https://www.youtube.com/watch?v=HKFuDP4tPP8',
                'image' => '/images/services/previs.webp',
                'span' => 'md:col-span-2',
                'show_in_portfolio' => true,
                'sort_order' => 10,
            ],
            [
                'title' => 'AI Advertising',
                'video_url' => 'https://www.youtube.com/watch?v=hhh_-j2VzWM',
                'image' => '/images/services/ai_adver.webp',
                'span' => 'md:col-span-2',
                'show_in_portfolio' => true,
                'sort_order' => 20,
            ],
            [
                'title' => 'AI Post Production',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
                'image' => '/images/services/post.webp',
                'span' => 'md:col-span-2',
                'show_in_portfolio' => true,
                'sort_order' => 30,
            ],
            [
                'title' => 'AI Models & Influencers',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreetAndDirt.mp4',
                'image' => '/images/services/content.webp',
                'span' => 'md:col-span-2',
                'show_in_portfolio' => true,
                'sort_order' => 40,
            ],
            [
                'title' => 'Micro Drama Series',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4',
                'image' => '/images/services/microdrama.webp',
                'span' => 'md:col-span-2',
                'show_in_portfolio' => true,
                'sort_order' => 50,
            ]
        ];

        foreach ($portfolios as $data) {
            $serviceTitle = $data['title'];
            if ($serviceTitle === 'Micro Drama Series') {
                $serviceTitle = 'Micro Drama';
            }
            $service = \App\Models\Service::where('title', $serviceTitle)->first();
            $data['service_id'] = $service?->id;

            Portfolio::updateOrCreate(
                ['title' => $data['title']],
                $data
            );
        }
    }
}
