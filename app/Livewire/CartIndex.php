<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartIndex extends Component
{
    public function render()
    {
        return view('livewire.cart-index');
    }

    public $cartItems;

    public function mount()
    {
        $this->loadCartItems();
    }

    public function loadCartItems(): void
    {
        $user = Auth::user();
        if ($user && $user->cart) {
            $this->cartItems = CartItem::with('product')->where('cart_id', $user->cart->id)->get();
        } else {
            $this->cartItems = collect();
        }
    }

    public function incrementQuantity($index)
    {
        $item = $this->cartItems[$index];
        if ($item->quantity < $item->product->stock_quantity) {
            $item->increment('quantity');
        } else {
            $this->dispatch('alert', ['type' => 'warning', 'message' => 'Product is out of stock']);
        }
    }
    
    public function decrementQuantity($index)
    {
        $item = $this->cartItems[$index];
        if ($item->quantity > 1) {
            $item->decrement('quantity');
        }
    }

    public function removeItem($id)
    {
        CartItem::destroy($id);
        $this->loadCartItems();
        $this->dispatch('cartUpdated');
    }

    #[Computed]
    public function totalPrice(): int
    {
        return $this->cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }
}
