<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response
        ->assertStatus(200)
        ->assertSee('Cinematic')
        ->assertSee('Measurable Impact.');
});

it('renders marketing pages', function (string $path, string $copy) {
    $this->get($path)
        ->assertStatus(200)
        ->assertSee($copy);
})->with([
    ['/about', 'Filmmaking In Our DNA.'],
    ['/services', 'Rapid proof of concepts'],
    ['/contact', 'Ready to Scale Your Vision?'],
]);
