<?php

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->adminUser = User::factory()->create([
        'role' => 'admin',
    ]);
});

it('prevents non-admin users from accessing Portfolio data', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->getJson(route('admin.portfolios.index'));
    $response->assertStatus(403);
});

it('lists Portfolios successfully for admin users', function () {
    $portfolio = Portfolio::create([
        'title' => 'AI Previs',
        'span' => 'md:col-span-2',
        'video_url' => 'https://example.com/video1.mp4',
        'image' => '/images/work1.png',
        'show_in_portfolio' => true,
        'sort_order' => 10,
    ]);

    $response = $this->actingAs($this->adminUser)->getJson(route('admin.portfolios.index'));

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'title' => 'AI Previs',
        'video_url' => 'https://example.com/video1.mp4',
        'image' => '/images/work1.png',
        'span' => 'md:col-span-2',
    ]);
});

it('creates a new Portfolio item successfully', function () {
    $service = \App\Models\Service::create([
        'num' => '01',
        'title' => 'Test Service',
        'description' => 'Test Desc',
        'bullets' => [],
        'accent' => '#fff',
    ]);

    $payload = [
        'title' => 'New Ad Film',
        'service_id' => $service->id,
        'video_url' => 'https://youtube.com/watch?v=hello',
        'video_aspect_ratio' => '9:16',
        'span' => 'md:col-span-3',
        'show_in_portfolio' => true,
        'sort_order' => 10,
    ];

    $response = $this->actingAs($this->adminUser)->postJson(route('admin.portfolios.store'), $payload);

    $response->assertStatus(201);
    $this->assertDatabaseHas('portfolios', [
        'title' => 'New Ad Film',
        'service_id' => $service->id,
        'video_url' => 'https://youtube.com/watch?v=hello',
        'video_aspect_ratio' => '9:16',
        'span' => 'md:col-span-3',
        'show_in_portfolio' => true,
        'sort_order' => 10,
    ]);
});

it('resizes uploaded Portfolio images to a 16:9 frame', function () {
    $response = $this->actingAs($this->adminUser)->post(route('admin.portfolios.store'), [
        'title' => 'Uploaded Film',
        'video_url' => '',
        'span' => 'md:col-span-3',
        'show_in_portfolio' => '1',
        'sort_order' => 20,
        'image_file' => UploadedFile::fake()->image('portfolio.jpg', 2400, 1600),
    ]);

    $response->assertCreated();

    $imagePath = public_path($response->json('portfolio.image'));

    try {
        [$width, $height] = getimagesize($imagePath);

        expect($width)->toBe(1600)
            ->and($height)->toBe(900)
            ->and($response->json('portfolio.image'))->toEndWith('.webp')
            ->and(mime_content_type($imagePath))->toBe('image/webp')
            ->and(filesize($imagePath))->toBeLessThan(500 * 1024);
    } finally {
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
});

it('preserves the vertical framing of portrait Portfolio cover uploads', function () {
    $response = $this->actingAs($this->adminUser)->post(route('admin.portfolios.store'), [
        'title' => 'Uploaded Vertical Film',
        'video_url' => '',
        'span' => 'md:col-span-3',
        'show_in_portfolio' => '1',
        'sort_order' => 21,
        'image_file' => UploadedFile::fake()->image('portfolio-portrait.jpg', 1200, 2400),
    ]);

    $response->assertCreated();

    $imagePath = public_path($response->json('portfolio.image'));

    try {
        [$width, $height] = getimagesize($imagePath);

        // Source is 1200x2400 (2:4 ratio); scaled to fit within 900x1600
        // without upscaling yields 800x1600.
        expect($width)->toBe(800)
            ->and($height)->toBe(1600)
            ->and($response->json('portfolio.image'))->toEndWith('.webp');
    } finally {
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
});

it('falls back to the default cover when the image is explicitly removed', function () {
    $portfolio = Portfolio::create([
        'title' => 'No Cover Anymore',
        'span' => 'md:col-span-2',
        'video_url' => '',
        'image' => '/images/portfolios/existing-cover.webp',
        'show_in_portfolio' => true,
        'sort_order' => 12,
    ]);

    $payload = [
        'title' => 'No Cover Anymore',
        'span' => 'md:col-span-2',
        'video_url' => '',
        'image' => '',
        'show_in_portfolio' => true,
        'sort_order' => 12,
    ];

    $response = $this->actingAs($this->adminUser)->patchJson(route('admin.portfolios.update', $portfolio), $payload);

    $response->assertStatus(200);
    $this->assertDatabaseHas('portfolios', [
        'id' => $portfolio->id,
        'image' => '/images/portfolio-default-cover.webp',
    ]);
});

it('updates an existing Portfolio item successfully', function () {
    $service = \App\Models\Service::create([
        'num' => '01',
        'title' => 'Test Service',
        'description' => 'Test Desc',
        'bullets' => [],
        'accent' => '#fff',
    ]);

    $portfolio = Portfolio::create([
        'title' => 'Old Title',
        'span' => 'md:col-span-2',
        'video_url' => 'https://example.com/old.mp4',
        'image' => '/images/old.png',
        'show_in_portfolio' => true,
        'sort_order' => 10,
    ]);

    $payload = [
        'title' => 'New Title',
        'service_id' => $service->id,
        'span' => 'md:col-span-4',
        'video_url' => 'https://example.com/new.mp4',
        'video_aspect_ratio' => '9:16',
        'show_in_portfolio' => false,
        'sort_order' => 10,
    ];

    $response = $this->actingAs($this->adminUser)->patchJson(route('admin.portfolios.update', $portfolio), $payload);

    $response->assertStatus(200);
    $this->assertDatabaseHas('portfolios', [
        'id' => $portfolio->id,
        'title' => 'New Title',
        'service_id' => $service->id,
        'span' => 'md:col-span-4',
        'video_url' => 'https://example.com/new.mp4',
        'video_aspect_ratio' => '9:16',
        'show_in_portfolio' => false,
        'sort_order' => 10,
    ]);
});

it('deletes a Portfolio item successfully', function () {
    $portfolio = Portfolio::create([
        'title' => 'Delete Me',
        'span' => 'md:col-span-2',
        'video_url' => 'https://example.com/delete.mp4',
        'image' => '/images/delete.png',
        'show_in_portfolio' => true,
        'sort_order' => 10,
    ]);

    $response = $this->actingAs($this->adminUser)->deleteJson(route('admin.portfolios.destroy', $portfolio));

    $response->assertStatus(200);
    $this->assertDatabaseMissing('portfolios', [
        'id' => $portfolio->id,
    ]);
});

it('reorders Portfolios successfully', function () {
    $portfolio1 = Portfolio::create([
        'title' => 'Portfolio 1',
        'span' => 'md:col-span-2',
        'video_url' => 'https://example.com/1.mp4',
        'image' => '/images/1.png',
        'show_in_portfolio' => true,
        'sort_order' => 10,
    ]);

    $portfolio2 = Portfolio::create([
        'title' => 'Portfolio 2',
        'span' => 'md:col-span-2',
        'video_url' => 'https://example.com/2.mp4',
        'image' => '/images/2.png',
        'show_in_portfolio' => true,
        'sort_order' => 20,
    ]);

    $response = $this->actingAs($this->adminUser)->patchJson(route('admin.portfolios.reorder'), [
        'ids' => [$portfolio2->id, $portfolio1->id],
    ]);

    $response->assertStatus(200);
    expect($portfolio2->fresh()->sort_order)->toBe(10);
    expect($portfolio1->fresh()->sort_order)->toBe(20);
});

it('reorders a filtered subset without changing other Portfolio sort slots', function () {
    $first = Portfolio::create([
        'title' => 'Filtered First',
        'span' => 'md:col-span-2',
        'image' => '/images/first.png',
        'show_in_portfolio' => true,
        'sort_order' => 10,
    ]);
    $untouched = Portfolio::create([
        'title' => 'Other Category',
        'span' => 'md:col-span-2',
        'image' => '/images/other.png',
        'show_in_portfolio' => true,
        'sort_order' => 20,
    ]);
    $last = Portfolio::create([
        'title' => 'Filtered Last',
        'span' => 'md:col-span-2',
        'image' => '/images/last.png',
        'show_in_portfolio' => true,
        'sort_order' => 30,
    ]);

    $this->actingAs($this->adminUser)
        ->patchJson(route('admin.portfolios.reorder'), [
            'ids' => [$last->id, $first->id],
        ])
        ->assertOk();

    expect($last->fresh()->sort_order)->toBe(10)
        ->and($untouched->fresh()->sort_order)->toBe(20)
        ->and($first->fresh()->sort_order)->toBe(30);
});
