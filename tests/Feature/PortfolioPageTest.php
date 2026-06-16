<?php

use App\Models\Director;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('portfolio page is displayed', function () {
    $this->get(route('portfolio'))->assertOk();
});

test('portfolio page aggregates and filters works', function () {
    // 1. Create mock directors with works
    Director::create([
        'slug' => 'sunil-thomas',
        'eyebrow' => 'Director 1',
        'first_name' => 'Sunil',
        'last_name' => 'Thomas',
        'role' => 'Director 1 role',
        'stats' => [],
        'bio_title_white' => 'Title',
        'bio_title_gradient' => 'Gradient',
        'bio_image' => '/images/bio.jpg',
        'bio_alt' => 'Bio image alt',
        'bio' => [],
        'works_eyebrow' => 'Expertise',
        'works_title_white' => 'White',
        'works_title_muted' => 'Muted',
        'works' => [
            [
                'image' => '/images/work1.png', 
                'title' => 'AI Advertising', 
                'span' => 'md:col-span-2',
                'video_url' => 'https://example.com/video1.mp4'
            ],
            [
                'image' => '/images/work2.png', 
                'title' => 'Cinematic Worlds', 
                'span' => 'md:col-span-2',
                'video_url' => 'https://example.com/video2.mp4'
            ]
        ],
    ]);

    Director::create([
        'slug' => 'maya-chen',
        'eyebrow' => 'Director 2',
        'first_name' => 'Maya',
        'last_name' => 'Chen',
        'role' => 'Director 2 role',
        'stats' => [],
        'bio_title_white' => 'Title',
        'bio_title_gradient' => 'Gradient',
        'bio_image' => '/images/bio.jpg',
        'bio_alt' => 'Bio image alt',
        'bio' => [],
        'works_eyebrow' => 'Expertise',
        'works_title_white' => 'White',
        'works_title_muted' => 'Muted',
        'works' => [
            [
                'image' => '/images/work3.png', 
                'title' => 'World Building', 
                'span' => 'md:col-span-2',
                'video_url' => 'https://example.com/video3.mp4'
            ]
        ],
    ]);

    // 2. Test Livewire component renders all works initially
    Livewire::test('pages::portfolio')
        ->assertSee('AI Advertising (by Sunil Thomas)')
        ->assertSee('Cinematic Worlds (by Sunil Thomas)')
        ->assertSee('World Building (by Maya Chen)');

    // 3. Test filtering by category
    Livewire::test('pages::portfolio')
        ->set('selectedCategory', 'World Building')
        ->assertSee('World Building (by Maya Chen)')
        ->assertDontSee('AI Advertising (by Sunil Thomas)')
        ->assertDontSee('Cinematic Worlds (by Sunil Thomas)');

    // 4. Test reset filters
    Livewire::test('pages::portfolio')
        ->set('selectedCategory', 'World Building')
        ->call('resetFilters')
        ->assertSee('AI Advertising (by Sunil Thomas)')
        ->assertSee('Cinematic Worlds (by Sunil Thomas)')
        ->assertSee('World Building (by Maya Chen)');
});
