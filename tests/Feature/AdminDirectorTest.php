<?php

use App\Models\Director;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->adminUser = User::factory()->create([
        'role' => 'admin',
    ]);
});

it('prevents non-admin users from accessing director data', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->getJson(route('admin.directors.index'));
    $response->assertStatus(403);
});

it('lists directors successfully for admin users', function () {
    $director = Director::create([
        'slug' => 'test-director',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'eyebrow' => 'Test Subtitle',
        'role' => 'Director',
        'bio_title_white' => 'White Title',
        'bio_title_gradient' => 'Gradient Title',
        'bio_image' => '/images/test.jpg',
        'bio_alt' => 'Alt text',
        'works_eyebrow' => 'Works Eyebrow',
        'works_title_white' => 'Works Title White',
        'works_title_muted' => 'Works Title Muted',
        'bio' => ['Paragraph 1', 'Paragraph 2'],
        'stats' => [
            ['value' => '100', 'suffix' => '+', 'label' => 'Label 1'],
            ['value' => '10', 'suffix' => 'yrs', 'label' => 'Label 2'],
            ['value' => '5', 'suffix' => '', 'label' => 'Label 3'],
        ],
        'works' => [
            ['image' => '/images/work.jpg', 'title' => 'Work 1', 'span' => 'col-span-2', 'video_url' => 'http://test.mp4'],
        ],
    ]);

    $response = $this->actingAs($this->adminUser)->getJson(route('admin.directors.index'));

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'slug' => 'test-director',
    ]);
});

it('creates a new director profile successfully', function () {
    $payload = [
        'first_name' => 'Alice',
        'last_name' => 'Smith',
        'slug' => 'alice-smith',
        'eyebrow' => 'AI Director · Studio',
        'role' => 'Creative Director',
        'bio_title_white' => 'White',
        'bio_title_gradient' => 'Gradient',
        'bio_image' => '/images/alice.jpg',
        'bio_alt' => 'Alice photo',
        'works_eyebrow' => 'Core',
        'works_title_white' => 'White Title',
        'works_title_muted' => 'Muted Title',
        'bio_raw' => "Paragraph 1\n\nParagraph 2",
        'stat_1_value' => '50',
        'stat_1_suffix' => '+',
        'stat_1_label' => 'Films',
        'stat_2_value' => '8',
        'stat_2_suffix' => 'yrs',
        'stat_2_label' => 'Exp',
        'stat_3_value' => '2',
        'stat_3_suffix' => '',
        'stat_3_label' => 'Awards',
        'works_raw' => json_encode([
            ['image' => '/images/work1.png', 'title' => 'Commercial A', 'span' => 'col-span-2', 'video_url' => 'http://video.mp4']
        ]),
    ];

    $response = $this->actingAs($this->adminUser)->postJson(route('admin.directors.store'), $payload);

    $response->assertStatus(201);
    $this->assertDatabaseHas('directors', [
        'first_name' => 'Alice',
        'last_name' => 'Smith',
        'slug' => 'alice-smith',
    ]);
});

it('updates an existing director successfully', function () {
    $director = Director::create([
        'slug' => 'john-doe',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'eyebrow' => 'AI Director · Vidhya Studio',
        'role' => 'Commercial Director',
        'bio_title_white' => 'White',
        'bio_title_gradient' => 'Gradient',
        'bio_image' => '/images/john.jpg',
        'bio_alt' => 'John photo',
        'works_eyebrow' => 'Core',
        'works_title_white' => 'White Title',
        'works_title_muted' => 'Muted Title',
        'bio' => ['Paragraph 1'],
        'stats' => [
            ['value' => '100', 'suffix' => '+', 'label' => 'Label 1'],
            ['value' => '10', 'suffix' => 'yrs', 'label' => 'Label 2'],
            ['value' => '5', 'suffix' => '', 'label' => 'Label 3'],
        ],
        'works' => [],
    ]);

    $payload = [
        'first_name' => 'Johnathan',
        'last_name' => 'Doe',
        'slug' => 'johnathan-doe',
        'eyebrow' => 'AI Director · Vidhya Studio',
        'role' => 'Lead AI Director',
        'bio_title_white' => 'White',
        'bio_title_gradient' => 'Gradient',
        'bio_image' => '/images/john.jpg',
        'bio_alt' => 'John photo',
        'works_eyebrow' => 'Core',
        'works_title_white' => 'White Title',
        'works_title_muted' => 'Muted Title',
        'bio_raw' => "Updated Paragraph 1\nUpdated Paragraph 2",
        'stat_1_value' => '120',
        'stat_1_suffix' => '+',
        'stat_1_label' => 'Films',
        'stat_2_value' => '12',
        'stat_2_suffix' => 'yrs',
        'stat_2_label' => 'Exp',
        'stat_3_value' => '6',
        'stat_3_suffix' => '',
        'stat_3_label' => 'Awards',
        'works_raw' => '[]',
    ];

    $response = $this->actingAs($this->adminUser)->patchJson(route('admin.directors.update', $director), $payload);

    $response->assertStatus(200);
    $this->assertDatabaseHas('directors', [
        'id' => $director->id,
        'first_name' => 'Johnathan',
        'slug' => 'johnathan-doe',
        'role' => 'Lead AI Director',
    ]);
});

it('deletes a director successfully', function () {
    $director = Director::create([
        'slug' => 'to-be-deleted',
        'first_name' => 'Delete',
        'last_name' => 'Me',
        'eyebrow' => 'AI Director · Vidhya Studio',
        'role' => 'Temporary',
        'bio_title_white' => 'White',
        'bio_title_gradient' => 'Gradient',
        'bio_image' => '/images/delete.jpg',
        'bio_alt' => 'Photo',
        'works_eyebrow' => 'Core',
        'works_title_white' => 'White Title',
        'works_title_muted' => 'Muted Title',
        'bio' => ['Paragraph 1'],
        'stats' => [
            ['value' => '100', 'suffix' => '+', 'label' => 'Label 1'],
            ['value' => '10', 'suffix' => 'yrs', 'label' => 'Label 2'],
            ['value' => '5', 'suffix' => '', 'label' => 'Label 3'],
        ],
        'works' => [],
    ]);

    $response = $this->actingAs($this->adminUser)->deleteJson(route('admin.directors.destroy', $director));

    $response->assertStatus(200);
    $this->assertDatabaseMissing('directors', [
        'id' => $director->id,
    ]);
});

it('uploads and resizes bio image file successfully', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('sunil.png', 800, 600);

    $payload = [
        'first_name' => 'Bob',
        'last_name' => 'Vance',
        'slug' => 'bob-vance',
        'eyebrow' => 'AI Director · Studio',
        'role' => 'Creative Director',
        'bio_title_white' => 'White',
        'bio_title_gradient' => 'Gradient',
        'bio_image_file' => $file,
        'bio_alt' => 'Bob photo',
        'works_eyebrow' => 'Core',
        'works_title_white' => 'White Title',
        'works_title_muted' => 'Muted Title',
        'bio_raw' => "Paragraph 1\nParagraph 2",
        'stat_1_value' => '50',
        'stat_1_suffix' => '+',
        'stat_1_label' => 'Films',
        'stat_2_value' => '8',
        'stat_2_suffix' => 'yrs',
        'stat_2_label' => 'Exp',
        'stat_3_value' => '2',
        'stat_3_suffix' => '',
        'stat_3_label' => 'Awards',
        'works_raw' => '[]',
    ];

    $response = $this->actingAs($this->adminUser)->postJson(route('admin.directors.store'), $payload);

    $response->assertStatus(201);
    
    $director = Director::where('slug', 'bob-vance')->first();
    expect($director->bio_image)->toStartWith('/images/directors/bob-vance_');

    $path = public_path($director->bio_image);
    if (file_exists($path)) {
        unlink($path);
    }
});

it('automatically extracts youtube thumbnail covers when work image is blank', function () {
    $payload = [
        'first_name' => 'Youtube',
        'last_name' => 'Tester',
        'slug' => 'youtube-tester',
        'eyebrow' => 'AI Director · Studio',
        'role' => 'Creative Director',
        'bio_title_white' => 'White',
        'bio_title_gradient' => 'Gradient',
        'bio_image' => '/images/youtube.jpg',
        'bio_alt' => 'Tester photo',
        'works_eyebrow' => 'Core',
        'works_title_white' => 'White Title',
        'works_title_muted' => 'Muted Title',
        'bio_raw' => "Paragraph 1\nParagraph 2",
        'stat_1_value' => '50',
        'stat_1_suffix' => '+',
        'stat_1_label' => 'Films',
        'stat_2_value' => '8',
        'stat_2_suffix' => 'yrs',
        'stat_2_label' => 'Exp',
        'stat_3_value' => '2',
        'stat_3_suffix' => '',
        'stat_3_label' => 'Awards',
        'works_raw' => json_encode([
            [
                'image' => '',
                'title' => 'Test YT Video',
                'span' => 'md:col-span-2',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
            ],
            [
                'image' => '/images/preset-cover.jpg',
                'title' => 'Preset Image Video',
                'span' => 'md:col-span-3',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
            ]
        ]),
    ];

    $response = $this->actingAs($this->adminUser)->postJson(route('admin.directors.store'), $payload);

    $response->assertStatus(201);

    $director = Director::where('slug', 'youtube-tester')->first();
    expect($director)->not->toBeNull();
    expect($director->works)->toHaveCount(2);

    // First work: image was empty, should be set to YouTube thumbnail
    expect($director->works[0]['image'])->toBe('https://img.youtube.com/vi/dQw4w9WgXcQ/mqdefault.jpg');

    // Second work: image was preset, should remain unchanged
    expect($director->works[1]['image'])->toBe('/images/preset-cover.jpg');
});

it('deletes old bio image files when updated or deleted', function () {
    Storage::fake('public');

    // Create a dummy old image file in public/images/directors
    $oldFilename = 'cleanup_old_' . time() . '.jpg';
    $oldPath = public_path('images/directors/' . $oldFilename);
    @mkdir(dirname($oldPath), 0755, true);
    file_put_contents($oldPath, 'dummy content');
    expect(file_exists($oldPath))->toBeTrue();

    // Create a director referencing this image
    $director = Director::create([
        'slug' => 'cleanup-test',
        'first_name' => 'Cleanup',
        'last_name' => 'Test',
        'eyebrow' => 'AI Director · Studio',
        'role' => 'Creative Director',
        'bio_title_white' => 'White',
        'bio_title_gradient' => 'Gradient',
        'bio_image' => '/images/directors/' . $oldFilename,
        'bio_alt' => 'photo',
        'works_eyebrow' => 'Core',
        'works_title_white' => 'White Title',
        'works_title_muted' => 'Muted Title',
        'bio' => ['Paragraph 1'],
        'stats' => [
            ['value' => '50', 'suffix' => '+', 'label' => 'Films'],
            ['value' => '8', 'suffix' => 'yrs', 'label' => 'Exp'],
            ['value' => '2', 'suffix' => '', 'label' => 'Awards'],
        ],
        'works' => [],
    ]);

    // Update the director with a new file upload
    $newFile = UploadedFile::fake()->image('new.png', 800, 600);
    $payload = [
        'first_name' => 'Cleanup',
        'last_name' => 'Test',
        'slug' => 'cleanup-test',
        'eyebrow' => 'AI Director · Studio',
        'role' => 'Creative Director',
        'bio_title_white' => 'White',
        'bio_title_gradient' => 'Gradient',
        'bio_image' => '/images/directors/' . $oldFilename,
        'bio_image_file' => $newFile,
        'bio_alt' => 'photo',
        'works_eyebrow' => 'Core',
        'works_title_white' => 'White Title',
        'works_title_muted' => 'Muted Title',
        'bio_raw' => "Paragraph 1",
        'stat_1_value' => '50',
        'stat_1_suffix' => '+',
        'stat_1_label' => 'Films',
        'stat_2_value' => '8',
        'stat_2_suffix' => 'yrs',
        'stat_2_label' => 'Exp',
        'stat_3_value' => '2',
        'stat_3_suffix' => '',
        'stat_3_label' => 'Awards',
        'works_raw' => '[]',
    ];

    $response = $this->actingAs($this->adminUser)->postJson(route('admin.directors.update', $director), array_merge($payload, [
        'id' => $director->id,
        '_method' => 'PATCH',
    ]));

    $response->assertStatus(200);

    // Old file should be deleted
    expect(file_exists($oldPath))->toBeFalse();

    // New file should exist
    $director->refresh();
    $newPath = public_path($director->bio_image);
    expect(file_exists($newPath))->toBeTrue();

    // Delete the director entirely
    $response = $this->actingAs($this->adminUser)->deleteJson(route('admin.directors.destroy', $director));
    $response->assertStatus(200);

    // New file should also be deleted
    expect(file_exists($newPath))->toBeFalse();
});

it('creates a general studio profile with only core details and works', function () {
    $payload = [
        'first_name' => 'Vidhya',
        'last_name' => 'Studio',
        'slug' => 'general',
        'works_raw' => json_encode([
            [
                'image' => '/images/work1.png',
                'title' => 'General Studio Work A',
                'span' => 'md:col-span-2',
                'video_url' => 'https://youtube.com/watch?v=123',
                'show_in_portfolio' => true,
            ]
        ]),
    ];

    $response = $this->actingAs($this->adminUser)->postJson(route('admin.directors.store'), $payload);

    $response->assertStatus(201);
    $this->assertDatabaseHas('directors', [
        'first_name' => 'Vidhya',
        'last_name' => 'Studio',
        'slug' => 'general',
        'eyebrow' => 'Vidhya Studio',
        'role' => 'Studio',
        'works_eyebrow' => 'Works',
        'works_title_white' => 'Studio',
        'works_title_muted' => 'Works',
    ]);
});



