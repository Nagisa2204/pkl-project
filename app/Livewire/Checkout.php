<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Checkout extends Component
{
    public $buyer_name;
    public $buyer_whatsapp;
    public $shipping_address_id;

    public function mount()
    {
        $user = Auth::user();
        
        if ($user->userAddresses()->count() === 0) {
            $this->dispatch('alert', ['type' => 'warning', 'message' => 'Please add address on profile.']);
            return redirect()->route('profile.show');
        }

        $this->buyer_name = $user->name;
        $this->buyer_whatsapp = $user->phone;
        $this->shipping_address_id = $user->userAddresses()->first()->id;
    }

    public function getCardProperty()
    {
        return Cart::with('cartItems.product')->where('user_id', Auth::id())->first();
    }

    public function placeOrder()
    {
        $this->validate([
            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_whatsapp' => ['required', 'string', 'max:255'],
        ]);

        if (Auth::user()->userAddresses()->count() === 0) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Address not found.']);
            return;
        }

        $cart = $this->cart;

        if (!$cart || $cart->cartItems->isEmpty()) {
            $this->dispatch('alert', ['type' => 'warning', 'message' => 'Cart is empty.']);
            return;
        }

        $order = null;

        DB::transaction(function() use ($cart, &$order) {
            $subtotal = 0;
            $itemToCreate = [];

            foreach ($cart->cartItems as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                if ($product->stock_quantity < $item->quantity) {
                    throw new \Exception('Product is out of stock');
                }

                $product->decrement('stock_quantity', $item->quantity);
                $subtotal += $item->price * $item->quantity;

                $itemToCreate[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'product_price' => $product->price,
                    'quantity' => $item->quantity,
                    'weight_grams' => $product->weight_grams,
                    'stock_status' => $product->stock_status,
                    'subtotal' => $item->price * $item->quantity,
                ];
            }

            $shippingCost = 0;
            $total = $subtotal + $shippingCost;

            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_no' => 'INV-' . date('Ymd') . '-' . Str::random(6),
                'buyer_name' => $this->buyer_name,
                'buyer_email' => Auth::user()->email,
                'buyer_whatsapp' => $this->buyer_whatsapp,
                'shipping_address_id' => $this->shipping_address_id ?? 0,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'delivery_status' => 'pending',
                'fulfillment_status' => 'pending',
            ]);

            foreach ($itemToCreate as $orderItemData) {
                $orderItemData['order_id'] = $order->id;
                OrderItem::create($orderItemData);
            }

            $cart->delete();
        });
        $this->dispatch('cartUpdated');
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Order placed successfully.']);
        
        return redirect()->route('order.show', $order->invoice_no);
    }

        public function render()
    {
        return view('livewire.checkout', [
            'cart' => $this->cart
        ]);
    }
}
