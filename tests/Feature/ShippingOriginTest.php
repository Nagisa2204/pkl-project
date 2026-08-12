<?php

use App\Models\StoreSetting;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\ShippingAddressService;
use App\Services\ShippingRateService;
use App\Services\StoreSettingsService;
use Illuminate\Support\Facades\Http;

function createShippingStore(array $overrides = []): StoreSetting
{
    $store = StoreSetting::query()->updateOrCreate(['key' => 'default'], array_merge([
        'store_name' => 'Toko Uji',
        'address' => 'Jalan Pickup No. 1',
        'province' => 'Nusa Tenggara Barat',
        'city' => 'Kota Mataram',
        'district' => 'Sekarbela',
        'subdistrict' => 'Tanjung Karang',
        'postal_code' => '83115',
        'shipping_origin_id' => 101,
        'shipping_origin_label' => 'Tanjung Karang, Sekarbela, Kota Mataram, Nusa Tenggara Barat',
    ], $overrides));

    app(StoreSettingsService::class)->forget();

    return $store;
}

function createShippingDestination(): UserAddress
{
    $user = User::factory()->create();

    return UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Rumah',
        'receiver_name' => $user->name,
        'phone' => '08123456789',
        'address_line' => 'Jalan Tujuan No. 2',
        'province_name' => 'Nusa Tenggara Barat',
        'city_name' => 'Lombok Tengah',
        'district_name' => 'Praya',
        'subdistrict_name' => 'Praya',
        'postal_code' => '83511',
        'destination_id' => 202,
        'destination_label' => 'Praya, Lombok Tengah, Nusa Tenggara Barat',
        'is_default' => true,
    ]);
}

beforeEach(function () {
    config([
        'rajaongkir.api_key' => 'test-key',
        'rajaongkir.base_url' => 'https://shipping.test/api/v1',
        'rajaongkir.default_couriers' => ['jne'],
    ]);
    app(StoreSettingsService::class)->forget();
});

test('shipping rates use the latest store address as origin', function () {
    createShippingStore();
    $destination = createShippingDestination();
    $origins = [];

    Http::fake(function ($request) use (&$origins) {
        $origins[] = (int) $request['origin'];

        return Http::response(['data' => [[
            'service' => 'REG',
            'description' => 'Reguler',
            'cost' => 15000,
            'etd' => '2-3 hari',
        ]]]);
    });

    app(ShippingRateService::class)->rates($destination, 1000, 'jne');

    StoreSetting::query()->where('key', 'default')->update([
        'shipping_origin_id' => 303,
        'shipping_origin_label' => 'Origin Baru',
    ]);
    app(StoreSettingsService::class)->forget();

    app(ShippingRateService::class)->rates($destination, 1000, 'jne');

    expect($origins)->toBe([101, 303]);
});

test('shipping calculation rejects an incomplete store origin before contacting gateway', function () {
    createShippingStore(['shipping_origin_id' => null]);
    Http::fake();

    expect(fn () => app(ShippingAddressService::class)->origin())
        ->toThrow(RuntimeException::class, 'Alamat asal toko belum lengkap');

    Http::assertNothingSent();
});
