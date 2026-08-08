<?php

use App\Models\StoreSetting;
use App\Services\StoreSettingsService;
use Illuminate\Support\Facades\Cache;

test('store settings cache contains attributes instead of a serialized model', function () {
    $settings = app(StoreSettingsService::class)->get();

    expect($settings)->toBeInstanceOf(StoreSetting::class)
        ->and($settings->exists)->toBeTrue()
        ->and(Cache::get('store.settings.attributes.v2'))->toBeArray()
        ->and(Cache::get('store.settings.attributes.v2'))->not->toBeInstanceOf(StoreSetting::class);
});

test('forget removes both the current and legacy store settings cache', function () {
    Cache::forever('store.settings', unserialize('O:19:"MissingStoreSetting":0:{}'));
    app(StoreSettingsService::class)->get();

    app(StoreSettingsService::class)->forget();

    expect(Cache::has('store.settings'))->toBeFalse()
        ->and(Cache::has('store.settings.attributes.v2'))->toBeFalse();
});

test('store settings are refreshed after the cache is forgotten', function () {
    $service = app(StoreSettingsService::class);
    $settings = $service->get();

    StoreSetting::query()->whereKey($settings->getKey())->update([
        'store_name' => 'Updated Store',
    ]);

    $service->forget();

    expect($service->get()->store_name)->toBe('Updated Store');
});
