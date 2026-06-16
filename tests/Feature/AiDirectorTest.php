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
