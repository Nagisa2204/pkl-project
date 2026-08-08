<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function add(User $user, int $variantId, int $quantity = 1): CartItem
    {
        return DB::transaction(function () use ($user, $variantId, $quantity) {
            $variant = ProductVariant::with('product')->lockForUpdate()->findOrFail($variantId);
            $minimum = max(1, $variant->product->min_order_quantity);
            $quantity = max($minimum, $quantity);
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_variant_id', $variant->id)
                ->lockForUpdate()
                ->first();
            $newQuantity = ($item?->quantity ?? 0) + $quantity;

            if (! $variant->isPurchasable($newQuantity)) {
                throw ValidationException::withMessages(['cart' => 'Stok varian yang dipilih tidak mencukupi.']);
            }

            return CartItem::updateOrCreate(
                ['cart_id' => $cart->id, 'product_variant_id' => $variant->id],
                ['quantity' => $newQuantity, 'price' => $variant->price]
            );
        }, attempts: 3);
    }

    public function setQuantity(User $user, int $itemId, int $quantity): void
    {
        DB::transaction(function () use ($user, $itemId, $quantity) {
            $item = CartItem::query()
                ->whereHas('cart', fn ($query) => $query->where('user_id', $user->id))
                ->with('variant.product')
                ->lockForUpdate()
                ->findOrFail($itemId);
            $quantity = max($item->variant->product->min_order_quantity, $quantity);

            if (! $item->variant->isPurchasable($quantity)) {
                throw ValidationException::withMessages(['cart' => 'Stok varian yang dipilih tidak mencukupi.']);
            }

            $item->update(['quantity' => $quantity, 'price' => $item->variant->price]);
        }, attempts: 3);
    }

    public function remove(User $user, int $itemId): void
    {
        CartItem::query()
            ->whereHas('cart', fn ($query) => $query->where('user_id', $user->id))
            ->findOrFail($itemId)
            ->delete();
    }
}
