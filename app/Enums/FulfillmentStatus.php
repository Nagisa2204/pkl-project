<?php

namespace App\Enums;

use App\Enums\Concerns\ProvidesEnumOptions;

enum FulfillmentStatus: string
{
    use ProvidesEnumOptions;

    case Unfulfilled = 'unfulfilled';
    case Processing = 'processing';
    case Fulfilled = 'fulfilled';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Unfulfilled => 'Belum Dipenuhi',
            self::Processing => 'Sedang Dipenuhi',
            self::Fulfilled => 'Terpenuhi',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Fulfilled => 'success',
            self::Cancelled => 'danger',
            self::Processing => 'warning',
            self::Unfulfilled => 'secondary',
        };
    }
}
