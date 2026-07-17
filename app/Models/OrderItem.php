<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property string $product_name
 * @property string $sku
 * @property int $product_price
 * @property int $quantity
 * @property int $weight_grams
 * @property string $stock_status
 * @property int $subtotal
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */

#[Fillable(['order_id', 'product_id', 'product_name', 'sku', 'product_price', 'quantity', 'weight_grams', 'stock_status', 'subtotal'])]
class OrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
