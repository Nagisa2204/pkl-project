<?php

namespace App\Enums;

use App\Enums\Concerns\ProvidesEnumOptions;

enum PaymentStatus: string
{
    use ProvidesEnumOptions;

    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Pembayaran',
            self::Paid => 'Sudah Dibayar',
            self::Failed => 'Pembayaran Gagal',
            self::Expired => 'Kedaluwarsa',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Paid => 'success',
            self::Failed, self::Expired => 'danger',
            self::Pending => 'warning',
        };
    }

    public function isFinal(): bool
    {
        return $this !== self::Pending;
    }
}
