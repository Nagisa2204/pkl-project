<?php

namespace App\Data;

final readonly class ShippingQuote
{
    /**
     * @param array<string, mixed> $rate
     */
    public function __construct(
        public ShippingLocation $origin,
        public ShippingLocation $destination,
        public array $rate,
    ) {}

    public function cost(): int
    {
        return (int) ($this->rate['cost'] ?? 0);
    }

    /**
     * @return array<string, mixed>
     */
    public function rawResponse(): array
    {
        return [
            'origin' => ['id' => $this->origin->providerId, 'label' => $this->origin->label],
            'destination' => ['id' => $this->destination->providerId, 'label' => $this->destination->label],
            'rate' => $this->rate,
        ];
    }
}
