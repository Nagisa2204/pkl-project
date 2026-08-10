<?php

namespace App\Enums;

use App\Enums\Concerns\ProvidesEnumOptions;

enum StockStatus: string
{
    use ProvidesEnumOptions;

    case Available = 'available';
    case Preorder = 'preorder';
    case OutOfStock = 'out_of_stock';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Tersedia',
            self::Preorder => 'Preorder',
            self::OutOfStock => 'Habis',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Available => 'success',
            self::Preorder => 'warning',
            self::OutOfStock => 'danger',
        };
    }
}
