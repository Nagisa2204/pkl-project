<?php

namespace App\Services;

use App\Data\ShippingQuote;
use App\Models\UserAddress;

class ShippingRateService
{
    public function __construct(
        private readonly ShippingAddressService $addresses,
        private readonly RajaOngkirService $gateway,
    ) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function rates(UserAddress $destination, int $weightGrams, string $courier): array
    {
        return $this->gateway->rates(
            $this->addresses->origin(),
            $this->addresses->destination($destination),
            $weightGrams,
            $courier,
        );
    }

    public function authoritativeRate(
        UserAddress $destination,
        int $weightGrams,
        string $courier,
        string $serviceCode,
    ): ShippingQuote {
        $origin = $this->addresses->origin();
        $destinationLocation = $this->addresses->destination($destination);
        $rate = $this->gateway->authoritativeRate(
            $origin,
            $destinationLocation,
            $weightGrams,
            $courier,
            $serviceCode,
        );

        return new ShippingQuote($origin, $destinationLocation, $rate);
    }
}
