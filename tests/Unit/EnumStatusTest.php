<?php

use App\Enums\OrderStatus;
use App\Enums\ShipmentStatus;

test('ecommerce enums expose Indonesian labels and safe shipment transitions', function () {
    expect(ShipmentStatus::Packing->label())->toBe('Dikemas')
        ->and(OrderStatus::Processing->label())->toBe('Diproses')
        ->and(ShipmentStatus::Pending->canTransitionTo(ShipmentStatus::Packing))->toBeTrue()
        ->and(ShipmentStatus::Packing->canTransitionTo(ShipmentStatus::Shipped))->toBeTrue()
        ->and(ShipmentStatus::Shipped->canTransitionTo(ShipmentStatus::Delivered))->toBeTrue()
        ->and(ShipmentStatus::Delivered->canTransitionTo(ShipmentStatus::Packing))->toBeFalse();
});
