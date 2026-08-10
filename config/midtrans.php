<?php

$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOL);
$defaultSnapUrl = $isProduction
    ? 'https://app.midtrans.com/snap/snap.js'
    : 'https://app.sandbox.midtrans.com/snap/snap.js';
$snapUrl = trim((string) env('MIDTRANS_SNAP_URL', ''));

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => $isProduction,
    'is_sanitized' => filter_var(env('MIDTRANS_IS_SANITIZED', true), FILTER_VALIDATE_BOOL),
    'is_3ds' => filter_var(env('MIDTRANS_IS_3DS', true), FILTER_VALIDATE_BOOL),
    'snap_url' => $snapUrl !== '' ? $snapUrl : $defaultSnapUrl,
    'expiry_minutes' => (int) env('MIDTRANS_EXPIRY_MINUTES', 60),
];
