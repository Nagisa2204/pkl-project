<?php

namespace App\Services;

use App\Data\ShippingLocation;
use App\Models\UserAddress;
use RuntimeException;

class ShippingAddressService
{
    public function __construct(private readonly StoreSettingsService $settings) {}

    public function origin(): ShippingLocation
    {
        $store = $this->settings->get();
        $required = [
            $store->shipping_origin_id,
            $store->shipping_origin_label,
            $store->address,
            $store->province,
            $store->city,
            $store->district,
            $store->subdistrict,
            $store->postal_code,
        ];

        if (collect($required)->contains(fn ($value) => blank($value))) {
            throw new RuntimeException('Alamat asal toko belum lengkap. Lengkapi alamat pickup pada Pengaturan Toko.');
        }

        return new ShippingLocation(
            providerId: (int) $store->shipping_origin_id,
            label: (string) $store->shipping_origin_label,
            address: collect([
                $store->address,
                $store->subdistrict,
                $store->district,
                $store->city,
                $store->province,
                $store->postal_code,
            ])->filter()->implode(', '),
        );
    }

    public function destination(UserAddress $address): ShippingLocation
    {
        $required = [
            $address->destination_id,
            $address->destination_label,
            $address->address_line,
            $address->province_name,
            $address->city_name,
            $address->district_name,
            $address->subdistrict_name,
            $address->postal_code,
        ];

        if (collect($required)->contains(fn ($value) => blank($value))) {
            throw new RuntimeException('Alamat tujuan belum lengkap. Perbarui alamat pengiriman pada halaman profil.');
        }

        return new ShippingLocation(
            providerId: (int) $address->destination_id,
            label: (string) $address->destination_label,
            address: collect([
                $address->address_line,
                $address->subdistrict_name,
                $address->district_name,
                $address->city_name,
                $address->province_name,
                $address->postal_code,
            ])->filter()->implode(', '),
        );
    }
}
