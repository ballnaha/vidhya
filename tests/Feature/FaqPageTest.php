<?php

use App\Models\Faq;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('faq page is displayed', function () {
    $this->get(route('faq'))->assertOk();
});

test('faq page search filters questions, answers, categories, and keywords', function () {
    Faq::create([
        'category' => 'First Category',
        'question' => 'How to do basic integration?',
        'answer' => 'Use the provided API key.',
        'keywords' => 'simple, setup',
        'sort_order' => 1,
    ]);

    Faq::create([
        'category' => 'Second Category',
        'question' => 'Advanced techniques for scaling?',
        'answer' => 'Use horizontally scaled instances.',
        'keywords' => 'complex, infrastructure',
        'sort_order' => 2,
    ]);

    // Search by question word
    Livewire::test('pages::faq')
        ->assertSee('How to do basic integration?')
        ->assertSee('Advanced techniques for scaling?')
        ->set('search', 'integration')
        ->assertSeeHtml('How to do basic <mark style="background-color: rgba(54, 107, 195, 0.35); color: #ffffff; padding: 0px 4px; border: 1px solid rgba(54, 107, 195, 0.3); border-radius: 4px; font-weight: 600;">integration</mark>?')
        ->assertDontSee('Advanced techniques for scaling?');

    // Search by answer word
    Livewire::test('pages::faq')
        ->set('search', 'horizontally')
        ->assertSee('Advanced techniques for scaling?')
        ->assertSeeHtml('Use <mark style="background-color: rgba(54, 107, 195, 0.35); color: #ffffff; padding: 0px 4px; border: 1px solid rgba(54, 107, 195, 0.3); border-radius: 4px; font-weight: 600;">horizontally</mark> scaled instances.')
        ->assertDontSee('How to do basic integration?');

    // Search by category
    Livewire::test('pages::faq')
        ->set('search', 'First Category')
        ->assertSee('How to do basic integration?')
        ->assertDontSee('Advanced techniques for scaling?');

    // Search by keyword
    Livewire::test('pages::faq')
        ->set('search', 'complex')
        ->assertSee('Advanced techniques for scaling?')
        ->assertDontSee('How to do basic integration?');

    // Test reset search
    Livewire::test('pages::faq')
        ->set('search', 'complex')
        ->call('resetSearch')
        ->assertSee('How to do basic integration?')
        ->assertSee('Advanced techniques for scaling?');
});
