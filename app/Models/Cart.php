<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;


/**
 * @property int $id
 * @property int $user_id
 * @property string $session_id
 * @property string $owner_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */

#[Fillable(['user_id', 'session_id', 'owner_key'])]
#[Hidden(['session_id', 'owner_key'])]
class Cart extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
