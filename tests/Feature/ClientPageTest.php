<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('client page is accessible', function () {
    $this->get(route('client'))
        ->assertOk()
        ->assertSee('Trusted By')
        ->assertSee('Brands we create with');
});
