<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartIndex extends Component
{
    public function getCartProducts()
    {
        return Cart::with('cartItems.product.images')->where('user_id', Auth::id())->where('status', 'active')->first();
    }

    public function incrementQuantity($itemId)
    {
        DB::transaction(function () use ($itemId) {
            $cartItem = CartItem::findOrFail($itemId);
            
            $product = Product::lockForUpdate()->findOrFail($cartItem->product_id);

            if ($cartItem->quantity < $product->stock_quantity) {
                $cartItem->increment('quantity', 1);
            } else {
                $this->dispatch('alert', ['type' => 'warning', 'message' => 'Product is out of stock']);
            }
        });

        $this->dispatch('cartUpdated');
    }
    
    public function decrementQuantity($itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);

        if ($cartItem->quantity > 1) {
            $cartItem->decrement('quantity', 1);
        }
        $this->dispatch('cartUpdated');
    }

    public function removeItem($itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);
        $cartItem->delete();

        $this->dispatch('alert', ['type' => 'info', 'message' => 'Produk berhasil dihapus.']);
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.cart-index', [
            'cart' => $this->getCartProducts()
        ]);
    }
}
