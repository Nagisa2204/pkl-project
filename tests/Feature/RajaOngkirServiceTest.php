<?php

use App\Exceptions\RajaOngkirException;
use App\Services\RajaOngkirService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::flush();
    config([
        'rajaongkir.api_key' => 'test-key',
        'rajaongkir.base_url' => 'https://shipping.test/api/v1',
        'rajaongkir.region_cache_ttl' => 0,
    ]);
});

test('it rejects the example api key before sending a request', function () {
    config(['rajaongkir.api_key' => 'YOUR_API_KEY_HERE']);
    Http::fake();

    expect(fn () => app(RajaOngkirService::class)->provinces())
        ->toThrow(RajaOngkirException::class, 'API key RajaOngkir belum diisi');

    Http::assertNothingSent();
});

test('it gives an actionable message when the api key is rejected', function () {
    Http::fake([
        '*' => Http::response(['meta' => ['message' => 'Unauthenticated']], 401),
    ]);

    expect(fn () => app(RajaOngkirService::class)->provinces())
        ->toThrow(RajaOngkirException::class, 'API key RajaOngkir tidak valid');
});

test('it detects provider errors even when the http response is successful', function () {
    Http::fake([
        '*' => Http::response([
            'meta' => [
                'message' => 'Invalid API key',
                'code' => 401,
                'status' => 'error',
            ],
            'data' => null,
        ], 200),
    ]);

    expect(fn () => app(RajaOngkirService::class)->provinces())
        ->toThrow(RajaOngkirException::class, 'API key RajaOngkir tidak valid');
});

test('it accepts the official v2 response envelope', function () {
    Http::fake([
        '*' => Http::response([
            'meta' => ['message' => 'Success Get Province', 'code' => 200, 'status' => 'success'],
            'data' => [['id' => 1, 'name' => 'NUSA TENGGARA BARAT']],
        ], 200),
    ]);

    expect(app(RajaOngkirService::class)->provinces())
        ->toBe([['id' => 1, 'name' => 'NUSA TENGGARA BARAT']]);
});

test('it accepts compatible nested response envelopes', function (array $payload) {
    Http::fake(['*' => Http::response($payload, 200)]);

    expect(app(RajaOngkirService::class)->provinces())
        ->toBe([['id' => 1, 'name' => 'Bali']]);
})->with([
    'nested data results' => [['data' => ['results' => [['id' => 1, 'name' => 'Bali']]]]],
    'root results' => [['results' => [['id' => 1, 'name' => 'Bali']]]],
    'legacy envelope' => [['rajaongkir' => ['results' => [['id' => 1, 'name' => 'Bali']]]]],
]);

test('it rejects a successful response with an invalid data shape', function () {
    Http::fake([
        '*' => Http::response(['data' => null], 200),
    ]);

    expect(fn () => app(RajaOngkirService::class)->provinces())
        ->toThrow(RajaOngkirException::class, 'Format respons data wilayah');
});

test('it caches region lists to avoid repeated provider requests', function () {
    config(['rajaongkir.region_cache_ttl' => 3600]);
    Http::fake([
        '*' => Http::response(['data' => [['id' => 1, 'name' => 'Bali']]], 200),
    ]);

    $service = app(RajaOngkirService::class);

    expect($service->provinces())->toHaveCount(1)
        ->and($service->provinces())->toHaveCount(1);

    Http::assertSentCount(1);
});
