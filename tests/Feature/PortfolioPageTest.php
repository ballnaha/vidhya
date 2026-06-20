<?php

use App\Models\Portfolio;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('portfolio page is displayed', function () {
    $this->get(route('portfolio'))->assertOk();
});

test('portfolio page aggregates and filters works', function () {
    $service1 = \App\Models\Service::create([
        'num' => '01',
        'title' => 'AI Advertising',
        'description' => 'Desc 1',
        'bullets' => [],
        'accent' => '#fff',
        'sort_order' => 10,
    ]);
    
    $service2 = \App\Models\Service::create([
        'num' => '02',
        'title' => 'Cinematic Worlds',
        'description' => 'Desc 2',
        'bullets' => [],
        'accent' => '#000',
        'sort_order' => 20,
    ]);

    // 1. Create mock portfolio items
    Portfolio::create([
        'title' => 'AI Advertising Work',
        'service_id' => $service1->id,
        'span' => 'md:col-span-2',
        'video_url' => 'https://example.com/video1.mp4',
        'image' => '/images/work1.png',
        'show_in_portfolio' => true,
        'sort_order' => 10,
    ]);

    Portfolio::create([
        'title' => 'Cinematic Worlds Work',
        'service_id' => $service2->id,
        'span' => 'md:col-span-2',
        'video_url' => 'https://example.com/video2.mp4',
        'image' => '/images/work2.png',
        'show_in_portfolio' => true,
        'sort_order' => 20,
    ]);

    // 2. Test Livewire component renders all works
    Livewire::test('pages::portfolio')
        ->assertSee('AI Advertising Work')
        ->assertSee('Cinematic Worlds Work');

    // 3. Test filtering by service
    Livewire::test('pages::portfolio')
        ->set('selectedServiceId', $service1->id)
        ->assertSee('AI Advertising Work')
        ->assertDontSee('Cinematic Worlds Work');

    // 4. Test reset filters
    Livewire::test('pages::portfolio')
        ->set('selectedServiceId', $service1->id)
        ->call('resetFilters')
        ->assertSee('AI Advertising Work')
        ->assertSee('Cinematic Worlds Work');
});

test('portfolio page hides works marked as hide_from_portfolio', function () {
    Portfolio::create([
        'title' => 'Visible Work',
        'span' => 'md:col-span-2',
        'video_url' => 'https://example.com/video1.mp4',
        'image' => '/images/work1.png',
        'show_in_portfolio' => true,
        'sort_order' => 10,
    ]);

    Portfolio::create([
        'title' => 'Hidden Work',
        'span' => 'md:col-span-2',
        'video_url' => 'https://example.com/video2.mp4',
        'image' => '/images/work2.png',
        'show_in_portfolio' => false,
        'sort_order' => 20,
    ]);

    Livewire::test('pages::portfolio')
        ->assertSee('Visible Work')
        ->assertDontSee('Hidden Work');
});

test('portfolio page groups videos above still images', function () {
    Portfolio::create([
        'title' => 'Still Created First',
        'span' => 'md:col-span-2',
        'image' => '/images/still-first.png',
        'show_in_portfolio' => true,
        'sort_order' => 10,
    ]);

    Portfolio::create([
        'title' => 'Video Created Second',
        'span' => 'md:col-span-2',
        'video_url' => 'https://example.com/video.mp4',
        'image' => '/images/video-second.png',
        'show_in_portfolio' => true,
        'sort_order' => 20,
    ]);

    Livewire::test('pages::portfolio')
        ->assertSeeInOrder([
            'Films &amp; Video',
            'Video Created Second',
            'Images &amp; Stills',
            'Still Created First',
        ], false);
});

test('portfolio page applies each work display size to the grid', function () {
    Portfolio::create([
        'title' => 'Wide Portfolio Work',
        'span' => 'md:col-span-4',
        'image' => '/images/work-wide.png',
        'show_in_portfolio' => true,
        'sort_order' => 10,
    ]);

    Livewire::test('pages::portfolio')
        ->assertSee('Wide Portfolio Work')
        ->assertSeeHtml('md:col-span-4');
});

test('portfolio page exposes the configured portrait video ratio', function () {
    Portfolio::create([
        'title' => 'Portrait Portfolio Work',
        'span' => 'md:col-span-2',
        'video_url' => 'https://example.com/portrait.mp4',
        'video_aspect_ratio' => '9:16',
        'image' => '/images/work-portrait.png',
        'show_in_portfolio' => true,
        'sort_order' => 10,
    ]);

    Livewire::test('pages::portfolio')
        ->assertSee('Portrait Portfolio Work')
        ->assertSeeHtml("modalVideoAspectRatio = '9:16'");
});
