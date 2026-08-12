<?php

namespace App\Data;

final readonly class ShippingLocation
{
    public function __construct(
        public int $providerId,
        public string $label,
        public string $address,
    ) {}
}
