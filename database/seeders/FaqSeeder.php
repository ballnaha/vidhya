<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate existing records to avoid duplicates when re-seeding
        Faq::truncate();

        $faqs = [
            // Category: Workflow & Timeline
            [
                'category' => 'Workflow & Timeline',
                'question' => 'How much faster is AI video production compared to traditional filming?',
                'answer' => 'Vidhya Studio utilizes "AI Speed" to deliver premium visual outputs in a fraction of the traditional production time. By leveraging highly structured AI pipelines and defined workflows, we eliminate logistical bottlenecks, ensuring faster output without sacrificing the human creative touch.',
                'keywords' => 'AI video production speed vs traditional filming, fast AI video creation, Vidhya Studio workflow efficiency',
                'sort_order' => 10,
            ],
            [
                'category' => 'Workflow & Timeline',
                'question' => 'How does Vidhya Studio combine "Knowledgeable Human Craft" with AI technology?',
                'answer' => 'Our process is led by experienced directors who provide "Knowledgeable Human Craft" to guide AI tools. Unlike traditional agencies that may rely on automation alone, we use technology as an extension of a director\'s vision, ensuring every frame maintains a soulful, human-centric cinematic perspective.',
                'keywords' => 'human-led AI creative process, AI video director expertise, collaborative AI filmmaking',
                'sort_order' => 20,
            ],

            // Category: Quality & Scalability
            [
                'category' => 'Quality & Scalability',
                'question' => 'Can AI video truly deliver cinematic quality comparable to traditional films?',
                'answer' => 'Yes. Backed by the legacy of Benetone Films, Vidhya Studio applies two decades of cinematic excellence to AI. We direct AI to handle depth, lighting, and composition with a "Cinematic Perspective," ensuring that the output is indistinguishable from high-end traditional film production.',
                'keywords' => 'cinematic AI video quality, professional AI filmmaking, Vidhya Studio video standard',
                'sort_order' => 10,
            ],
            [
                'category' => 'Quality & Scalability',
                'question' => 'How can AI help scale a "Hero Film" into targeted marketing assets?',
                'answer' => 'Through "Seamless Campaign Scaling," we can take a single core video asset and use AI to re-develop and scale it into hundreds of high-quality, targeted variations. This allows brands to maintain a consistent message while personalizing content for diverse audience segments at unprecedented speed.',
                'keywords' => 'scale video campaigns with AI, personalized marketing videos, programmatic video creation',
                'sort_order' => 20,
            ],

            // Category: Data Security & Brand Identity
            [
                'category' => 'Data Security & Brand Identity',
                'question' => 'What security protocols are in place to protect a brand\'s data and assets?',
                'answer' => 'Vidhya Studio adheres to "Ethical Craft & Strict Security." We implement rigorous data security protocols to ensure that all brand assets and intellectual property remain confidential and protected throughout the AI model training and production phases.',
                'keywords' => 'AI video production security, secure brand data AI, ethical AI filmmaking protocols',
                'sort_order' => 10,
            ],
            [
                'category' => 'Data Security & Brand Identity',
                'question' => 'How does Strategic Visual Development ensure brand consistency in AI outputs?',
                'answer' => 'Our "Strategic Visual Development" involves building bespoke AI models tailored to your specific brand identity. This ensures that the core message, aesthetics, and visual DNA remain consistent across all AI-generated content, preventing any deviation from your brand\'s established guidelines.',
                'keywords' => 'consistent brand AI video, custom trained AI models, strategic visual development guidelines',
                'sort_order' => 20,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
