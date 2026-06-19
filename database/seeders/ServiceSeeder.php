<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'num' => '01',
                'title' => 'AI POCs & Previs',
                'description' => 'Rapid proof of concepts, moodboards, and compelling pitch materials to validate your vision before full production begins.',
                'bullets' => ['Concept validation', 'Moodboard & storyboard', 'Pitch-ready materials', 'Fast turnaround'],
                'accent' => '#366bc3',
                'image' => '/images/services/previs.webp',
                'sort_order' => 10,
            ],
            [
                'num' => '02',
                'title' => 'AI Advertising',
                'description' => 'Complete digital campaign creation from the core concept to striking key visuals, delivered in a fraction of traditional timelines.',
                'bullets' => ['End-to-end campaign creation', 'Key visual development', 'Multi-platform formats', 'Performance-engineered briefs'],
                'accent' => '#823665',
                'image' => '/images/services/ai_adver.webp',
                'sort_order' => 20,
            ],
            [
                'num' => '03',
                'title' => 'AI Post Production',
                'description' => 'Cinematic finishing at scale. We take your hero films and expertly craft high quality variations to feed your entire marketing funnel.',
                'bullets' => ['Cinematic colour grading', 'Multi-format variations', 'Full funnel asset delivery', 'Broadcast-ready output'],
                'accent' => '#e60012',
                'image' => '/images/services/post.webp',
                'sort_order' => 30,
            ],
            [
                'num' => '04',
                'title' => 'AI Models & Influencers',
                'description' => 'Bespoke persona development and digital character creation for highly targeted brand representation.',
                'bullets' => ['Custom AI persona creation', 'Brand-aligned characters', 'Scalable digital talent', 'Consistent brand voice'],
                'accent' => '#366bc3',
                'image' => '/images/services/content.webp',
                'sort_order' => 40,
            ],
            [
                'num' => '05',
                'title' => 'Micro Drama',
                'description' => 'Highly engaging episodic digital narratives infused with our cinematic perspective and deep storytelling heritage.',
                'bullets' => ['Episodic narrative series', 'Cinematic storytelling', 'Platform-native formats', 'Audience retention focus'],
                'accent' => '#e60012',
                'image' => '/images/services/microdrama.webp',
                'sort_order' => 50,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['title' => $service['title']],
                $service
            );
        }
    }
}
