<?php

use App\Models\Director;
use Database\Seeders\DirectorSeeder;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DirectorSeeder::class);
});

it('renders the ai director page successfully', function () {
    $response = $this->get('/ai-director');

    $response->assertStatus(200);
    $response->assertSee('Sunil');
    $response->assertSee('Thomas');
    $response->assertSee('Maya');
    $response->assertSee('Chen');
});

it('excludes the general director profile from the roster', function () {
    Director::create([
        'slug' => 'general',
        'first_name' => 'Vidhya',
        'last_name' => 'Studio',
        'eyebrow' => 'Studio',
        'role' => 'General',
        'stats' => [],
        'bio_title_white' => 'White',
        'bio_title_gradient' => 'Gradient',
        'bio_image' => '/images/bio.jpg',
        'bio_alt' => 'Alt',
        'bio' => [],
        'works_eyebrow' => 'Works',
        'works_title_white' => 'White',
        'works_title_muted' => 'Muted',
        'works' => [],
    ]);

    $response = $this->get('/ai-director');

    $response->assertStatus(200);
    $response->assertDontSee('data-director-tab="general"', false);
});

