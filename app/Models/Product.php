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
 * @property string $name
 * @property string $category_id
 * @property string $slug
 * @property string $sku
 * @property string $description
 * @property int $price
 * @property string $stock_status
 * @property int $stock_quantity
 * @property int $weight_grams
 * @property int $preorder_days
 * @property int $min_order_quantity
 * @property string $thumbnail
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['category_id', 'name', 'description', 'price', 'slug', 'sku', 'stock_status', 'stock_quantity', 'weight_grams', 'preorder_days', 'min_order_quantity', 'thumbnail', 'is_active'])]
class Product extends Model
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
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
