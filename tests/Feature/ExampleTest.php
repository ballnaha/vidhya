<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('returns a successful response', function () {
    $response = $this->get('/');

    $response
        ->assertStatus(200)
        ->assertSee('Cinematic')
        ->assertSee('Measurable Impact.');
});

it('renders marketing pages', function (string $path, string $copy) {
    if ($path === '/services') {
        \App\Models\Service::create([
            'num' => '01',
            'title' => 'AI POCs & Previs',
            'description' => 'Rapid proof of concepts',
            'bullets' => ['Concept validation'],
            'accent' => '#366bc3',
            'image' => '/images/services/previs.webp',
            'sort_order' => 10,
        ]);
    }

    $this->get($path)
        ->assertStatus(200)
        ->assertSee($copy);
})->with([
    ['/about', 'Filmmaking In'],
    ['/services', 'Rapid proof of concepts'],
    ['/contact', 'Ready to Scale'],
]);

