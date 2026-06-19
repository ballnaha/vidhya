<?php

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->adminUser = User::factory()->create([
        'role' => 'admin',
    ]);
});

it('prevents non-admin users from accessing Service data', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->getJson(route('admin.services.index'));
    $response->assertStatus(403);
});

it('lists Services successfully for admin users', function () {
    $service = Service::create([
        'num' => '01',
        'title' => 'AI Previs',
        'description' => 'Test previs description.',
        'bullets' => ['Previs highlight 1', 'Previs highlight 2'],
        'accent' => '#123456',
        'image' => '/images/services/previs.webp',
        'sort_order' => 10,
    ]);

    $response = $this->actingAs($this->adminUser)->getJson(route('admin.services.index'));

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'num' => '01',
        'title' => 'AI Previs',
        'description' => 'Test previs description.',
        'accent' => '#123456',
    ]);
});

it('creates a new Service successfully', function () {
    $payload = [
        'num' => '02',
        'title' => 'AI Advertising Campaign',
        'description' => 'AI generated marketing details.',
        'bullets_raw' => "Highlight A\nHighlight B",
        'accent' => '#ff0000',
        'sort_order' => 5,
    ];

    $response = $this->actingAs($this->adminUser)->postJson(route('admin.services.store'), $payload);

    $response->assertStatus(201);
    $this->assertDatabaseHas('services', [
        'num' => '02',
        'title' => 'AI Advertising Campaign',
        'description' => 'AI generated marketing details.',
        'accent' => '#ff0000',
    ]);

    $service = Service::where('num', '02')->first();
    expect($service->bullets)->toBe(['Highlight A', 'Highlight B']);
    expect($service->image)->toBeNull();
});

it('updates an existing Service successfully', function () {
    $service = Service::create([
        'num' => '03',
        'title' => 'AI Old Service',
        'description' => 'Old content.',
        'bullets' => ['Old bullet'],
        'accent' => '#000000',
        'image' => '/images/services/previs.webp',
        'sort_order' => 20,
    ]);

    $payload = [
        'num' => '03-updated',
        'title' => 'AI New Service',
        'description' => 'Updated content.',
        'bullets_raw' => "New bullet 1\nNew bullet 2",
        'accent' => '#ffffff',
        'sort_order' => 12,
    ];

    $response = $this->actingAs($this->adminUser)->patchJson(route('admin.services.update', $service), $payload);

    $response->assertStatus(200);
    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'num' => '03-updated',
        'title' => 'AI New Service',
        'description' => 'Updated content.',
        'accent' => '#ffffff',
    ]);

    $updated = $service->fresh();
    expect($updated->bullets)->toBe(['New bullet 1', 'New bullet 2']);
});

it('deletes a Service successfully', function () {
    $service = Service::create([
        'num' => '04',
        'title' => 'Delete Me Service',
        'description' => 'Delete content.',
        'bullets' => ['Bullet delete'],
        'accent' => '#777777',
        'image' => '/images/services/previs.webp',
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($this->adminUser)->deleteJson(route('admin.services.destroy', $service));

    $response->assertStatus(200);
    $this->assertDatabaseMissing('services', [
        'id' => $service->id,
    ]);

    $indexResponse = $this->actingAs($this->adminUser)
        ->getJson(route('admin.services.index'))
        ->assertOk()
        ->assertJsonMissing(['id' => $service->id]);

    expect($indexResponse->headers->get('Cache-Control'))->toContain('no-store');
});

it('reorders Services successfully', function () {
    $service1 = Service::create([
        'num' => '01',
        'title' => 'Service 1',
        'description' => 'Desc 1',
        'bullets' => ['highlight'],
        'accent' => '#111',
        'image' => '/images/services/previs.webp',
        'sort_order' => 10,
    ]);

    $service2 = Service::create([
        'num' => '02',
        'title' => 'Service 2',
        'description' => 'Desc 2',
        'bullets' => ['highlight'],
        'accent' => '#222',
        'image' => '/images/services/previs.webp',
        'sort_order' => 20,
    ]);

    $response = $this->actingAs($this->adminUser)->patchJson(route('admin.services.reorder'), [
        'ids' => [$service2->id, $service1->id],
    ]);

    $response->assertStatus(200);
    expect($service2->fresh()->sort_order)->toBe(10);
    expect($service1->fresh()->sort_order)->toBe(20);
});
