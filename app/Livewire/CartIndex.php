<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartIndex extends Component
{
    public function getCartProperty(): ?Cart
    {
        return Cart::with('cartItems.product.images', 'cartItems.product.category')->where('user_id', Auth::id())->first();
    }

    public function incrementQuantity(int $itemId): void
    {
        DB::transaction(function () use ($itemId) {
            $cartItem = CartItem::findOrFail($itemId);
            
            $product = $cartItem->product()->lockForUpdate()->first();

            if ($cartItem->quantity >= $product->stock_quantity) {
                $this->dispatch('alert', type: 'warning', message: 'Product is out of stock');
                return;
            }
            $cartItem->increment('quantity');
        });

        $this->dispatch('cartUpdated');
    }
    
    public function decrementQuantity(int $itemId): void
    {
        $cartItem = CartItem::findOrFail($itemId);

        if ($cartItem->quantity <= 1) {
            return;
        }

        $cartItem->decrement('quantity');

        $this->dispatch('cartUpdated');
    }


    public function removeItem(int $itemId): void
    {
        CartItem::findOrFail($itemId)->delete();

        $this->dispatch('alert', type: 'success', message: 'Product removed from cart');

        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.cart-index', [
            'cart' => $this->cart,
        ]);
    }
}
