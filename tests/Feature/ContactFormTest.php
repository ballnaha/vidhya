<?php

use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'services.resend.key' => 're_test_key',
        'services.contact.to' => 'hello@vidhyastudio.com',
        'services.recaptcha.secret_key' => 'recaptcha-secret',
        'services.recaptcha.score_threshold' => 0.5,
        'mail.from.address' => 'website@vidhyastudio.com',
        'mail.from.name' => 'Vidhya Studio',
    ]);
});

it('verifies recaptcha and sends contact inquiries through Resend', function () {
    Http::fake([
        'https://www.google.com/recaptcha/api/siteverify' => Http::response([
            'success' => true,
            'score' => 0.9,
            'action' => 'contact',
        ]),
        'https://api.resend.com/emails' => Http::response(['id' => 'email_123']),
    ]);

    $this->postJson(route('contact.store'), [
        'name' => 'Jane Client',
        'email' => 'jane@example.com',
        'company' => 'Example Brand',
        'service' => 'AI Advertising',
        'message' => 'We would like to discuss a new advertising campaign.',
        'recaptcha_token' => 'valid-token',
    ])->assertOk()
        ->assertJsonPath('message', 'Message received. We will be in touch within 24 hours.');

    Http::assertSent(fn ($request) => $request->url() === 'https://api.resend.com/emails'
        && $request['reply_to'] === 'jane@example.com'
        && $request['to'] === ['hello@vidhyastudio.com']
        && str_contains($request['html'], 'Example Brand'));
});

it('rejects contact inquiries with a low recaptcha score', function () {
    Http::fake([
        'https://www.google.com/recaptcha/api/siteverify' => Http::response([
            'success' => true,
            'score' => 0.2,
            'action' => 'contact',
        ]),
    ]);

    $this->postJson(route('contact.store'), [
        'name' => 'Spam Bot',
        'email' => 'bot@example.com',
        'message' => 'This submission should not send an email.',
        'recaptcha_token' => 'low-score-token',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('recaptcha_token');

    Http::assertNotSent(fn ($request) => $request->url() === 'https://api.resend.com/emails');
});

it('validates contact form fields before calling external services', function () {
    Http::fake();

    $response = $this->postJson(route('contact.store'), [
        'name' => '',
        'email' => 'invalid-email',
        'message' => 'short',
        'recaptcha_token' => '',
    ]);

    expect($response->status())->toBe(422)
        ->and(array_keys($response->json('errors')))->toContain('name', 'email', 'message', 'recaptcha_token');

    expect(Http::recorded())->toBeEmpty();
});
