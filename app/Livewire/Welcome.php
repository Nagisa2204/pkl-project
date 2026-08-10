<?php

namespace App\Livewire;

use App\Enums\PaymentStatus;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Welcome extends Component
{
    public function addToCart(int $variantId, CartService $cart): mixed
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $cart->add(Auth::user(), $variantId);
        $this->dispatch('cartUpdated');
        $this->dispatch('toast', variant: 'success', message: 'Produk ditambahkan ke keranjang.');

        return null;
    }

    public function render()
    {
        return view('livewire.welcome', [
            'featuredProducts' => Product::query()
                ->active()
                ->with(['images', 'category', 'defaultVariant', 'activeVariants'])
                ->withSum([
                    'orderItems as sold_quantity' => fn ($query) => $query
                        ->whereHas('order', fn ($orders) => $orders->where('payment_status', PaymentStatus::Paid->value)),
                ], 'quantity')
                ->whereHas('activeVariants')
                ->latest()
                ->limit(4)
                ->get(),
        ]);
    }
}
