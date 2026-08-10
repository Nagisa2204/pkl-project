<?php

return [
    'enabled' => (bool) env('CLOUDFLARE_TURNSTILE_ENABLED', false),
    'site_key' => env('CLOUDFLARE_TURNSTILE_SITE_KEY'),
    'secret_key' => env('CLOUDFLARE_TURNSTILE_SECRET_KEY'),
    'timeout' => (int) env('CLOUDFLARE_TURNSTILE_TIMEOUT', 10),
    'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
];
