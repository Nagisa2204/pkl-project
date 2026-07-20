<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartCounter extends Component
{
    #[On('cartUpdated')]
    public function render()
    {
        $count = 0;

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $count = CartItem::where('cart_id', $cart->id)->sum('quantity');
            }
        }
        return view('livewire.cart-counter', [
            'count' => $count
        ]);
    }
}