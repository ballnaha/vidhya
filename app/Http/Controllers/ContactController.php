<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'company' => ['nullable', 'string', 'max:150'],
            'service' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'recaptcha_token' => ['required', 'string', 'max:4096'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('Please check the form and try again.'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        if (! $this->recaptchaIsValid($validated['recaptcha_token'], $request->ip())) {
            return response()->json([
                'message' => __('We could not verify that you are human. Please try again.'),
                'errors' => [
                    'recaptcha_token' => [__('reCAPTCHA verification failed. Please try again.')],
                ],
            ], 422);
        }

        $apiKey = (string) config('services.resend.key');

        if ($apiKey === '') {
            Log::error('Contact email could not be sent because RESEND_API_KEY is not configured.');

            return response()->json([
                'message' => __('Email delivery is temporarily unavailable. Please contact us directly.'),
            ], 503);
        }

        $html = view('emails.contact-inquiry', $validated)->render();

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(15)
                ->post('https://api.resend.com/emails', [
                    'from' => config('mail.from.name').' <'.config('mail.from.address').'>',
                    'to' => [config('services.contact.to')],
                    'reply_to' => $validated['email'],
                    'subject' => 'New project inquiry from '.$validated['name'],
                    'html' => $html,
                ]);
        } catch (ConnectionException $exception) {
            report($exception);

            return response()->json([
                'message' => __('Email delivery is temporarily unavailable. Please try again.'),
            ], 502);
        }

        if (! $response->successful()) {
            Log::error('Resend rejected a contact form email.', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return response()->json([
                'message' => __('Email delivery is temporarily unavailable. Please try again.'),
            ], 502);
        }

        return response()->json([
            'message' => __('Message received. We will be in touch within 24 hours.'),
        ]);
    }

    private function recaptchaIsValid(string $token, ?string $ip): bool
    {
        $secret = (string) config('services.recaptcha.secret_key');

        if ($secret === '') {
            Log::error('Contact form verification failed because RECAPTCHA_SECRET_KEY is not configured.');

            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]);
        } catch (ConnectionException $exception) {
            report($exception);

            return false;
        }

        $result = $response->json();

        return $response->successful()
            && ($result['success'] ?? false) === true
            && ($result['action'] ?? '') === 'contact'
            && (float) ($result['score'] ?? 0) >= (float) config('services.recaptcha.score_threshold', 0.5);
    }
}
