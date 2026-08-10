<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $label
 * @property string $receiver_name
 * @property string $phone
 * @property string $address_line
 * @property string $province_name
 * @property string $city_name
 * @property string $district_name
 * @property string $subdistrict_name
 * @property string $postal_code
 * @property string $courier_note
 * @property int $destination_id
 * @property string $destination_label
 * @property bool $is_default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */

#[Fillable(['user_id', 'label', 'receiver_name', 'phone', 'address_line', 'province_name', 'city_name', 'district_name', 'subdistrict_name', 'postal_code', 'courier_note', 'destination_id', 'destination_label', 'is_default'])]
class UserAddress extends Model
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
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
