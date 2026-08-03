<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartIndex extends Component
{
    public array $selectedItems = [];
    public ?int $selectedCategoryId = null;
    public int $grandTotal = 0;
    public int $totalItems = 0;

    public function mount()
    {
        $this->calculateTotals();
    }

    public function getCartProperty(): ?Cart
    {
        return Cart::with('cartItems.product.images', 'cartItems.product.category')
            ->where('user_id', Auth::id())
            ->first();
    }

    public function updatedSelectedItems()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->grandTotal = 0;
        $this->totalItems = 0;
        $this->selectedCategoryId = null;

        if (empty($this->selectedItems)) {
            session()->put('selected_cart_items', []);
            return;
        }

        $cart = $this->cart;
        if (!$cart) return;

        $selectedCartItems = $cart->cartItems->whereIn('id', $this->selectedItems);

        if ($selectedCartItems->isNotEmpty()) {
            $this->selectedCategoryId = $selectedCartItems->first()->product->category_id;

            $validItems = $selectedCartItems->where('product.category_id', $this->selectedCategoryId);
            
            $this->selectedItems = $validItems->pluck('id')->map(fn($id) => (string) $id)->toArray();

            foreach ($validItems as $item) {
                $this->grandTotal += $item->product->price * $item->quantity;
                $this->totalItems += $item->quantity;
            }
            
            session()->put('selected_cart_items', $this->selectedItems);
        } else {
            session()->put('selected_cart_items', []);
        }
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
        $this->calculateTotals(); 
    }
    
    public function decrementQuantity(int $itemId): void
    {
        $cartItem = CartItem::findOrFail($itemId);

        if ($cartItem->quantity <= 1) {
            return;
        }

        $cartItem->decrement('quantity');

        $this->dispatch('cartUpdated');
        $this->calculateTotals();
    }

    public function removeItem(int $itemId): void
    {
        CartItem::findOrFail($itemId)->delete();

        if (($key = array_search($itemId, $this->selectedItems)) !== false) {
            unset($this->selectedItems[$key]);
        }

        $this->dispatch('alert', type: 'success', message: 'Product removed from cart');
        $this->dispatch('cartUpdated');
        $this->calculateTotals();
    }

    public function render()
    {
        return view('livewire.cart-index', [
            'cart' => $this->cart,
        ]);
    }
}