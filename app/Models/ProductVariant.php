<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'product_id', 'sku', 'combination_key', 'price', 'compare_at_price',
    'stock_quantity', 'stock_status', 'weight_grams', 'preorder_days',
    'is_default', 'is_active',
])]
class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'compare_at_price' => 'integer',
            'stock_quantity' => 'integer',
            'weight_grams' => 'integer',
            'preorder_days' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_variant_option_values',
            'product_variant_id',
            'product_option_value_id'
        )->withPivot('product_option_id')->withTimestamps();
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isPurchasable(int $quantity = 1): bool
    {
        if (! $this->is_active || ! $this->product?->is_active) {
            return false;
        }

        return $this->stock_status === 'preorder'
            || ($this->stock_status === 'available' && $this->stock_quantity >= $quantity);
    }

    public function displayName(): string
    {
        $values = $this->relationLoaded('optionValues')
            ? $this->optionValues->pluck('value')->filter()->implode(' / ')
            : $this->optionValues()->pluck('value')->implode(' / ');

        return $values ?: 'Default';
    }
}
