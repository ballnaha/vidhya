<?php

use App\Models\Service;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('contact page is displayed and lists services from database', function () {
    Service::create([
        'num' => '01',
        'title' => 'Custom Database Service Test',
        'description' => 'A description of the test service.',
        'bullets' => ['Test bullet 1', 'Test bullet 2'],
        'accent' => '#366bc3',
        'sort_order' => 5,
    ]);

    $this->get(route('contact'))
        ->assertOk()
        ->assertSee('Custom Database Service Test')
        ->assertSee('Other');
});
