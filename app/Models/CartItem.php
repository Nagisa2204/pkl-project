<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $cart_id
 * @property int $product_id
 * @property int $quantity
 * @property int $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
*/

#[Fillable(['cart_id', 'product_id', 'quantity', 'price'])]
class CartItem extends Model
{
    use HasFactory;

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);    
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
