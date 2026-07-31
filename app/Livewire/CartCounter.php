<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class CartCounter extends Component
{
    #[On('cartUpdated')]
    public function render()
    {
        $count = Auth::check()
            ? Auth::user()->cart?->cartItems()->sum('quantity') ?? 0
            : 0;

        return view('livewire.cart-counter', compact('count'));
    }
}