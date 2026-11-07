<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Verifies a Cloudflare Turnstile token on the server. Fails closed: any
 * error, timeout, or unexpected response counts as a failed check.
 */
class Turnstile implements ValidationRule
{
    public function __construct(private readonly ?string $ip = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! $this->verify($value)) {
            $fail('Please complete the security check and try again.');
        }
    }

    private function verify(string $token): bool
    {
        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post(config('billify.turnstile.verify_url'), [
                    'secret' => config('billify.turnstile.secret_key'),
                    'response' => $token,
                    'remoteip' => $this->ip,
                ]);
        } catch (ConnectionException) {
            return false;
        }

        return $response->successful() && $response->json('success') === true;
    }
}
