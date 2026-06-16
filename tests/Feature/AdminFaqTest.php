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
