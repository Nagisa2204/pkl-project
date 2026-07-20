<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ProductList extends Component
{   
    use WithPagination;

    public $search = '';
    public $selectedCategory = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function addToCart($productId) {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        DB::transaction(function () use ($user, $productId) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            $product = Product::lockForUpdate()->findOrFail($productId);

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
                    return;
                }
            }
        });
        $this->dispatch('cartUpdated');
    }

    public function render()    
    {
        $productsQuery = Product::with(['images', 'category'])->where('is_active', true);

        if (!empty($this->search)) {
            $productsQuery->where('name', 'like', '%' . $this->search . '%');
        }
    
        if ($this->selectedCategory) {
            $productsQuery->where('category_id', $this->selectedCategory);
        }

        return view('livewire.product-list', [
            'products' => $productsQuery->paginate(12),
            'categories' => Category::all() 
        ]);
    }
}