<?php

namespace App\Enums;

use App\Enums\Concerns\ProvidesEnumOptions;

enum OrderStatus: string
{
    use ProvidesEnumOptions;

    case PendingPayment = 'pending_payment';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'Menunggu Pembayaran',
            self::Processing => 'Diproses',
            self::Shipped => 'Dikirim',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Completed => 'success',
            self::Shipped => 'info',
            self::Cancelled => 'danger',
            default => 'warning',
        };
    }
}
