<?php

use App\Models\Service;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('services page is displayed and lists services from database', function () {
    Service::create([
        'num' => '99',
        'title' => 'AI Test Service Title',
        'description' => 'Test description of the service in database.',
        'bullets' => ['Dynamic feature 1', 'Dynamic feature 2'],
        'accent' => '#123456',
        'image' => '/images/services/previs.webp',
        'sort_order' => 1,
    ]);

    $this->get(route('services'))
        ->assertOk()
        ->assertSee('AI Test Service Title')
        ->assertSee('Test description of the service in database.')
        ->assertSee('Dynamic feature 1')
        ->assertSee('Dynamic feature 2');
});
