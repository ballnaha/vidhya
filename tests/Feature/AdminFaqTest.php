<?php

use App\Models\Faq;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->adminUser = User::factory()->create([
        'role' => 'admin',
    ]);
});

it('prevents non-admin users from accessing FAQ data', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->getJson(route('admin.faqs.index'));
    $response->assertStatus(403);
});

it('lists FAQs successfully for admin users', function () {
    $faq = Faq::create([
        'category' => 'Workflow & Timeline',
        'question' => 'How does it work?',
        'answer' => 'It works very well.',
        'keywords' => 'speed, workflow',
        'sort_order' => 10,
    ]);

    $response = $this->actingAs($this->adminUser)->getJson(route('admin.faqs.index'));

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'question' => 'How does it work?',
        'category' => 'Workflow & Timeline',
        'keywords' => 'speed, workflow',
    ]);
});

it('creates a new FAQ with a standard category successfully', function () {
    $payload = [
        'category' => 'Quality & Scalability',
        'question' => 'Is it scalable?',
        'answer' => 'Yes, very scalable.',
        'keywords' => 'scale, quality',
        'sort_order' => 5,
    ];

    $response = $this->actingAs($this->adminUser)->postJson(route('admin.faqs.store'), $payload);

    $response->assertStatus(201);
    $this->assertDatabaseHas('faqs', [
        'category' => 'Quality & Scalability',
        'question' => 'Is it scalable?',
        'keywords' => 'scale, quality',
    ]);
});

it('creates a new FAQ with a custom category successfully', function () {
    $payload = [
        'category' => 'Pricing & Plans',
        'question' => 'How much does it cost?',
        'answer' => 'Contact us for a quote.',
        'keywords' => 'price, cost',
        'sort_order' => 20,
    ];

    $response = $this->actingAs($this->adminUser)->postJson(route('admin.faqs.store'), $payload);

    $response->assertStatus(201);
    $this->assertDatabaseHas('faqs', [
        'category' => 'Pricing & Plans',
        'question' => 'How much does it cost?',
        'keywords' => 'price, cost',
    ]);
});

it('updates an existing FAQ successfully', function () {
    $faq = Faq::create([
        'category' => 'Workflow & Timeline',
        'question' => 'Will it change?',
        'answer' => 'No.',
        'keywords' => 'unchanged',
        'sort_order' => 15,
    ]);

    $payload = [
        'category' => 'New Custom Category',
        'question' => 'Has it changed?',
        'answer' => 'Yes it has.',
        'keywords' => 'changed, update',
        'sort_order' => 12,
    ];

    $response = $this->actingAs($this->adminUser)->patchJson(route('admin.faqs.update', $faq), $payload);

    $response->assertStatus(200);
    $this->assertDatabaseHas('faqs', [
        'id' => $faq->id,
        'category' => 'New Custom Category',
        'question' => 'Has it changed?',
        'keywords' => 'changed, update',
    ]);
});

it('deletes an FAQ successfully', function () {
    $faq = Faq::create([
        'category' => 'Workflow & Timeline',
        'question' => 'Delete me?',
        'answer' => 'Yes.',
        'keywords' => 'removal',
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($this->adminUser)->deleteJson(route('admin.faqs.destroy', $faq));

    $response->assertStatus(200);
    $this->assertDatabaseMissing('faqs', [
        'id' => $faq->id,
    ]);
});

it('reorders FAQs within a group without changing another group', function () {
    $first = Faq::create([
        'category' => 'Workflow & Timeline',
        'question' => 'First question?',
        'answer' => 'First answer.',
        'sort_order' => 10,
    ]);
    $otherGroup = Faq::create([
        'category' => 'Quality & Scalability',
        'question' => 'Other question?',
        'answer' => 'Other answer.',
        'sort_order' => 20,
    ]);
    $last = Faq::create([
        'category' => 'Workflow & Timeline',
        'question' => 'Last question?',
        'answer' => 'Last answer.',
        'sort_order' => 30,
    ]);

    $this->actingAs($this->adminUser)
        ->patchJson(route('admin.faqs.reorder'), [
            'ids' => [$last->id, $first->id],
        ])
        ->assertOk();

    expect($last->fresh()->sort_order)->toBe(10)
        ->and($otherGroup->fresh()->sort_order)->toBe(20)
        ->and($first->fresh()->sort_order)->toBe(30);
});

it('rejects duplicate FAQ priorities', function () {
    $first = Faq::create([
        'category' => 'Workflow & Timeline',
        'question' => 'Existing question?',
        'answer' => 'Existing answer.',
        'sort_order' => 10,
    ]);

    $this->actingAs($this->adminUser)
        ->postJson(route('admin.faqs.store'), [
            'category' => 'Workflow & Timeline',
            'question' => 'New question?',
            'answer' => 'New answer.',
            'keywords' => '',
            'sort_order' => 10,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('sort_order');

    $priorities = Faq::query()
        ->where('category', 'Workflow & Timeline')
        ->orderBy('sort_order')
        ->pluck('sort_order')
        ->all();

    expect($first->fresh()->sort_order)->toBe(10)
        ->and($priorities)->toBe([10]);
});

it('reorders all FAQs across groups', function () {
    $first = Faq::create([
        'category' => 'Group A',
        'question' => 'First?',
        'answer' => 'First.',
        'sort_order' => 10,
    ]);
    $second = Faq::create([
        'category' => 'Group B',
        'question' => 'Second?',
        'answer' => 'Second.',
        'sort_order' => 20,
    ]);
    $third = Faq::create([
        'category' => 'Group C',
        'question' => 'Third?',
        'answer' => 'Third.',
        'sort_order' => 30,
    ]);

    $this->actingAs($this->adminUser)
        ->patchJson(route('admin.faqs.reorder'), [
            'ids' => [$third->id, $first->id, $second->id],
        ])
        ->assertOk();

    expect($third->fresh()->sort_order)->toBe(10)
        ->and($first->fresh()->sort_order)->toBe(20)
        ->and($second->fresh()->sort_order)->toBe(30);
});
