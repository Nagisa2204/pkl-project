<?php

namespace App\Enums;

use App\Enums\Concerns\ProvidesEnumOptions;

enum ShipmentStatus: string
{
    use ProvidesEnumOptions;

    case Pending = 'pending';
    case Packing = 'packing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Diproses',
            self::Packing => 'Dikemas',
            self::Shipped => 'Dikirim',
            self::Delivered => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Delivered => 'success',
            self::Shipped => 'info',
            self::Cancelled => 'danger',
            self::Packing => 'warning',
            self::Pending => 'secondary',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        if ($this === $next) {
            return true;
        }

        return match ($this) {
            self::Pending => in_array($next, [self::Packing, self::Shipped, self::Cancelled], true),
            self::Packing => in_array($next, [self::Shipped, self::Cancelled], true),
            self::Shipped => $next === self::Delivered,
            self::Delivered, self::Cancelled => false,
        };
    }
}
