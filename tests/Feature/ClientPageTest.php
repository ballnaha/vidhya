<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('client page is accessible', function () {
    $this->get(route('client'))
        ->assertOk()
        ->assertSee('Trusted By')
        ->assertSee('Leading Brands');
});

test('client page uses the configured logo carousel speed', function () {
    \App\Models\SiteSetting::setValue(\App\Models\SiteSetting::CLIENT_CAROUSEL_SPEED, '55');

    $this->get(route('client'))
        ->assertOk()
        ->assertSee('data-client-carousel-speed="55"', false);
});
