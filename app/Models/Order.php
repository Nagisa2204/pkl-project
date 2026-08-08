<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $invoice_no
 * @property string $buyer_name
 * @property string $buyer_email
 * @property string $buyer_whatsapp
 * @property int $shipping_address_id
 * @property int $subtotal
 * @property int $shipping_cost
 * @property string $shipping_courier_code
 * @property string $shipping_courier_name
 * @property string $shipping_service_code
 * @property string $shipping_service_name
 * @property string $shipping_etd
 * @property int $total
 * @property string $status
 * @property string $payment_status
 * @property string $delivery_status
 * @property string $fulfillment_status
 * @property carbon|null $paid_at
 * @property carbon|null $stock_released_at
 * @property carbon|null $stock_shortage_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */

#[Fillable([
    'user_id', 'order_no', 'invoice_no', 'buyer_name', 'buyer_email', 'buyer_whatsapp',
    'shipping_address_id', 'subtotal', 'discount_total', 'shipping_cost', 'payment_fee',
    'shipping_courier_code', 'shipping_courier_name', 'shipping_service_code',
    'shipping_service_name', 'shipping_etd', 'total', 'payment_method', 'status',
    'payment_status', 'delivery_status', 'fulfillment_status', 'paid_at',
    'stock_reserved_at', 'stock_released_at', 'stock_shortage_at', 'cancelled_at',
    'completed_at',
])]
class Order extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'stock_released_at' => 'datetime',
            'stock_shortage_at' => 'datetime',
            'stock_reserved_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'shipping_address_id');   
    }
}
