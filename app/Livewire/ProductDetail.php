<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductDetail extends Component
{
    public Product $product;

    public function mount($slug)
    {
        $this->product = Product::with(['images', 'category'])->where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    public function addToCart() {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        DB::transaction(function () use ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id], ['status' => 'active']);

            $product = Product::lockForUpdate()->findOrFail($this->product->id);

            $cartItem = CartItem::where('cart_id', $cart->id)->where('product_id', $product->id)->first();

            if ($cartItem) {
                if ($cartItem->quantity < $product->stock_quantity) {
                    $cartItem->increment('quantity', 1);
                } else {
                    $this->dispatch('alert', ['type' => 'warning', 'message' => 'Product is out of stock']);
                    return;
                }
            } else {
                if ($product->stock_quantity > 0) {
                    CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'price' => $product->price,
                    ]);
                } else {
                    $this->dispatch('alert', ['type' => 'warning', 'message' => 'Product is out of stock']);
                }
            }
        });

        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.product-detail');
    }
}
