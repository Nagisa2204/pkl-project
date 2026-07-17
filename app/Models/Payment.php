<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $order_id
 * @property string $provider
 * @property string $provider_order_id
 * @property string $transaction_id
 * @property string $payment_type
 * @property string $bank
 * @property string $va_number
 * @property string $bill_key
 * @property int $biller_code
 * @property string $status
 * @property int $gross_amount
 * @property int $refund_amount
 * @property string $snap_token
 * @property string $redirect_url
 * @property Carbon|null $expiry_at
 * @property Carbon|null $paid_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */

#[Fillable(['order_id', 'provider', 'provider_order_id', 'transaction_id', 'payment_type', 'bank', 'va_number', 'bill_key', 'biller_code', 'status', 'gross_amount', 'refund_amount', 'snap_token', 'redirect_url', 'raw_response', 'expiry_at', 'paid_at'])]
#[Hidden(['raw_response', 'snap_token'])]
class Payment extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'raw_response' => AsArrayObject::class,
            'expiry_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
