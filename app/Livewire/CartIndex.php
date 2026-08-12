<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartIndex extends Component
{
    public array $selectedItems = [];
    public int $grandTotal = 0;
    public int $totalItems = 0;

    public function mount(): void
    {
        $this->calculateTotals();
    }

    public function getCartProperty(): ?Cart
    {
        return Cart::with(['cartItems.variant.product.images', 'cartItems.variant.optionValues.option'])
            ->where('user_id', Auth::id())->first();
    }

    public function updatedSelectedItems(): void
    {
        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $cart = $this->cart;
        $valid = $cart?->cartItems->whereIn('id', array_map('intval', $this->selectedItems)) ?? collect();
        $this->selectedItems = $valid->pluck('id')->map(fn ($id) => (string) $id)->all();
        $this->grandTotal = $valid->sum(fn ($item) => $item->variant->price * $item->quantity);
        $this->totalItems = $valid->sum('quantity');
        session()->put('selected_cart_items', $this->selectedItems);
    }

    public function incrementQuantity(int $itemId, CartService $cart): void
    {
        $item = $this->ownedItem($itemId);
        $cart->setQuantity(Auth::user(), $itemId, $item->quantity + 1);
        $this->afterCartChange();
    }

    public function decrementQuantity(int $itemId, CartService $cart): void
    {
        $item = $this->ownedItem($itemId);
        $minimum = $item->variant->product->min_order_quantity;
        if ($item->quantity > $minimum) {
            $cart->setQuantity(Auth::user(), $itemId, $item->quantity - 1);
            $this->afterCartChange();
        }
    }

    public function removeItem(int $itemId, CartService $cart): void
    {
        $cart->remove(Auth::user(), $itemId);
        $this->selectedItems = array_values(array_filter($this->selectedItems, fn ($id) => (int) $id !== $itemId));
        $this->afterCartChange();
        $this->dispatch('toast', variant: 'success', message: 'Produk dihapus dari keranjang.');
    }

    private function ownedItem(int $itemId)
    {
        return $this->cart?->cartItems->firstWhere('id', $itemId) ?? abort(404);
    }

    private function afterCartChange(): void
    {
        unset($this->cart);
        $this->dispatch('cartUpdated');
        $this->calculateTotals();
    }

    public function render()
    {
        return view('livewire.cart-index', ['cart' => $this->cart]);
    }
}
