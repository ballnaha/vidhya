<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use App\Models\Client;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('admin can open client management page', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'email_verified_at' => now()]);

    $this->actingAs($admin)->get(route('admin.clients'))
        ->assertOk()
        ->assertSee('New Client');
});

test('admin can upload a client logo', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'email_verified_at' => now()]);

    $this->actingAs($admin)->post(route('admin.clients.store'), [
        'name' => 'Example Brand',
        'logo_file' => UploadedFile::fake()->image('example.png', 600, 300),
        'website_url' => 'https://example.com',
        'sort_order' => 20,
        'is_active' => 1,
    ])->assertRedirect();

    $this->assertDatabaseHas('clients', ['name' => 'Example Brand', 'is_active' => true]);

    $client = Client::query()->where('name', 'Example Brand')->firstOrFail();
    if (file_exists(public_path($client->logo))) {
        unlink(public_path($client->logo));
    }
});

test('admin can reorder clients', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'email_verified_at' => now()]);
    $first = Client::create(['name' => 'First', 'logo' => '/images/client/BOSS.png', 'is_active' => true, 'sort_order' => 20]);
    $second = Client::create(['name' => 'Second', 'logo' => '/images/client/BOSS.png', 'is_active' => true, 'sort_order' => 30]);

    $this->actingAs($admin)->patchJson(route('admin.clients.reorder'), [
        'ids' => [$second->id, $first->id],
    ])->assertOk();

    expect($second->refresh()->sort_order)->toBe(10)
        ->and($first->refresh()->sort_order)->toBe(20);
});

test('admin client data endpoint returns client details', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'email_verified_at' => now()]);

    $this->actingAs($admin)->getJson(route('admin.clients.data'))
        ->assertOk()
        ->assertJsonPath('clients.0.name', 'Suntory Boss Coffee')
        ->assertJsonStructure(['clients' => [['id', 'name', 'logo', 'website_url', 'is_active', 'sort_order']]]);
});
