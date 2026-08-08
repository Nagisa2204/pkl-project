<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    public function verify(?string $token, ?string $ipAddress = null): bool
    {
        if (! config('turnstile.enabled')) {
            return true;
        }

        if (! $token || ! config('turnstile.secret_key')) {
            return false;
        }

        try {
            return Http::asForm()
                ->timeout(config('turnstile.timeout', 10))
                ->post(config('turnstile.verify_url'), [
                    'secret' => config('turnstile.secret_key'),
                    'response' => $token,
                    'remoteip' => $ipAddress,
                ])
                ->json('success') === true;
        } catch (\Throwable) {
            return false;
        }
    }
}
