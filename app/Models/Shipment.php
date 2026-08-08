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
 * @property int $user_address_id
 * @property string $receiver_name
 * @property string $phone
 * @property string $address_line
 * @property string $courier_note
 * @property int $destination_id
 * @property string $destination_label
 * @property string $courier_code
 * @property string $courier_name
 * @property string $service_code
 * @property string $service_name
 * @property int $cost
 * @property string $etd
 * @property string $awb_number
 * @property string $status
 * @property string $raw_response
 * @property Carbon|null $shipped_at
 * @property Carbon|null $delivered_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */

#[Fillable([
    'order_id', 'user_address_id', 'receiver_name', 'phone', 'address_line',
    'province_name', 'city_name', 'district_name', 'subdistrict_name', 'postal_code',
    'courier_note', 'destination_id', 'destination_label', 'courier_code',
    'courier_name', 'service_code', 'service_name', 'cost', 'etd', 'awb_number',
    'status', 'raw_response', 'shipped_at', 'delivered_at',
])]
#[Hidden(['raw_response'])]
class Shipment extends Model
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
            'raw_response' => AsArrayObject::class, 
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function userAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class);
    }
}
