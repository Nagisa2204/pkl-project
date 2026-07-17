<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $product_id
 * @property string $image_path
 * @property string $caption
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */

#[Fillable(['product_id', 'image_path', 'caption', 'sort_order'])]
class ProductImage extends Model
{
    use HasFactory;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
