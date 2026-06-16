<?php

namespace Database\Seeders;

use App\Models\Director;
use Illuminate\Database\Seeder;

class DirectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $directors = [
            [
                'slug' => 'sunil-thomas',
                'eyebrow' => 'AI Director · Vidhya Studio',
                'first_name' => 'Sunil',
                'last_name' => 'Thomas',
                'role' => 'Commercial Director·AI Filmmaker·300+ Brands',
                'stats' => [
                    ['value' => '300', 'suffix' => '+', 'label' => 'Commercials Directed'],
                    ['value' => '25', 'suffix' => 'yrs', 'label' => 'Industry Experience'],
                    ['value' => '4', 'suffix' => '', 'label' => 'Global Markets'],
                ],
                'bio_title_white' => 'Two Decades.',
                'bio_title_gradient' => 'One Vision.',
                'bio_image' => '/images/ai-director.jpg',
                'bio_alt' => 'Sunil Thomas directing with a headset',
                'bio' => [
                    'Sunil Thomas is an internationally recognised, award-winning commercial and content director working across the UK, USA, Asia, and Australia. He began his career in television in Sydney, creating promos for major broadcasters including SBS and Foxtel.',
                    'At 25, he transitioned into commercial directing full-time and quickly adopted a global outlook, building a career that spans London, the United States, and multiple international markets. To date, Sunil has directed over 300 commercials, collaborating with leading agencies and brands including Samsung, Red Bull, Visa, and Toyota.',
                    'His work is known for its strong visual identity, precise comedic timing, and emotionally engaging storytelling that resonates long after first viewing. Valued by agencies and clients alike for his consistency, clarity of vision, and ability to deliver ambitious ideas efficiently, his long list of repeat collaborators reflects both the quality of his work and his genuine passion for the craft.',
                ],
                'works_eyebrow' => 'Core Expertise',
                'works_title_white' => 'What He',
                'works_title_muted' => 'Brings',
                'works' => [
                    [
                        'image' => '/images/ai_director_work1.png', 
                        'title' => 'AI Advertising', 
                        'span' => 'md:col-span-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'
                    ],
                    [
                        'image' => '/images/ai-director.jpg', 
                        'title' => 'Cinematic Worlds', 
                        'span' => 'md:col-span-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'
                    ],
                    [
                        'image' => '/images/ai-director.jpg', 
                        'title' => 'Emotional Storytelling', 
                        'span' => 'md:col-span-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'
                    ],
                    [
                        'image' => '/images/ai-director.jpg', 
                        'title' => 'Director-Led Craft', 
                        'span' => 'md:col-span-2 md:col-start-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4'
                    ],
                    [
                        'image' => '/images/ai_director_work1.png', 
                        'title' => 'Brand Films', 
                        'span' => 'md:col-span-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4'
                    ],
                    [
                        'image' => '/images/ai_director_work2.png', 
                        'title' => 'Luxury Product Film', 
                        'span' => 'md:col-span-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4'
                    ],
                    [
                        'image' => '/images/ai_director_work3.png', 
                        'title' => 'Performance Story', 
                        'span' => 'md:col-span-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4'
                    ],
                ],
            ],
            [
                'slug' => 'maya-chen',
                'eyebrow' => 'AI Director · Vidhya Studio',
                'first_name' => 'Maya',
                'last_name' => 'Chen',
                'role' => 'Commercial Director·AI Filmmaker·300+ Brands',
                'stats' => [
                    ['value' => '100', 'suffix' => '+', 'label' => 'Commercials Directed'],
                    ['value' => '25', 'suffix' => 'yrs', 'label' => 'Industry Experience'],
                    ['value' => '4', 'suffix' => '', 'label' => 'Global Markets'],
                ],
                'bio_title_white' => 'Two Decades.',
                'bio_title_gradient' => 'One Vision.',
                'bio_image' => '/images/ai-director.jpg',
                'bio_alt' => 'AI director profile portrait',
                'bio' => [
                    'Sunil Thomas is an internationally recognised, award-winning commercial and content director working across the UK, USA, Asia, and Australia. He began his career in television in Sydney, creating promos for major broadcasters including SBS and Foxtel.',
                    'At 25, he transitioned into commercial directing full-time and quickly adopted a global outlook, building a career that spans London, the United States, and multiple international markets. To date, Sunil has directed over 300 commercials, collaborating with leading agencies and brands including Samsung, Red Bull, Visa, and Toyota.',
                    'His work is known for its strong visual identity, precise comedic timing, and emotionally engaging storytelling that resonates long after first viewing. Valued by agencies and clients alike for his consistency, clarity of vision, and ability to deliver ambitious ideas efficiently, his long list of repeat collaborators reflects both the quality of his work and his genuine passion for the craft.',
                ],
                'works_eyebrow' => 'Core Expertise',
                'works_title_white' => 'What She',
                'works_title_muted' => 'Brings',
                'works' => [
                    [
                        'image' => '/images/ai_director_work2.png', 
                        'title' => 'World Building', 
                        'span' => 'md:col-span-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreetAndDirt.mp4'
                    ],
                    [
                        'image' => '/images/ai_director_work3.png', 
                        'title' => 'Emotional Performance', 
                        'span' => 'md:col-span-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'
                    ],
                    [
                        'image' => '/images/ai_director_work1.png', 
                        'title' => 'AI Beauty Campaigns', 
                        'span' => 'md:col-span-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'
                    ],
                    [
                        'image' => '/images/ai-director.jpg', 
                        'title' => 'Hybrid Direction', 
                        'span' => 'md:col-span-2 md:col-start-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'
                    ],
                    [
                        'image' => '/images/ai_director_work2.png', 
                        'title' => 'Future Brand Films', 
                        'span' => 'md:col-span-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'
                    ],
                    [
                        'image' => '/images/ai_director_work3.png', 
                        'title' => 'Human Story System', 
                        'span' => 'md:col-span-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4'
                    ],
                    [
                        'image' => '/images/ai_director_work1.png', 
                        'title' => 'Digital Persona Launch', 
                        'span' => 'md:col-span-2',
                        'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4'
                    ],
                ],
            ],
        ];

        foreach ($directors as $data) {
            Director::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
