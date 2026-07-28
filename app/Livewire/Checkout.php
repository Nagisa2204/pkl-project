<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Checkout extends Component
{
    public $buyer_name;
    public $buyer_whatsapp;
    public $shipping_address_id;

    protected function rules(): array
    {
        return [
            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_whatsapp' => ['required', 'string', 'max:255'],
        ];
    }

    public function mount(): void
    {
        $user = Auth::user();

        if ($user->userAddresses()->doesntExist()) {
            $this->dispatch('alert', type: 'warning', message: 'Please add your shipping address first.');
            $this->redirectRoute('profile.show');
            return;
        }

        $address = $user->userAddresses()->first();

        $this->buyer_name = $user->name;
        $this->buyer_whatsapp = $user->phone;
        $this->shipping_address_id = $address->id;
    }

    public function getCartProperty(): ?Cart
    {
        return Cart::with('cartItems.product')->where('user_id', Auth::id())->first();}

    public function placeOrder()
    {
        $this->validate();
        $user = Auth::user();

        if ($user->userAddresses()->doesntExist()) {
            $this->dispatch('alert', type: 'error', message: 'Shipping address not found');
            return;
        }

        $cart = $this->cart;

        if (!$cart || $cart->cartItems->isEmpty()) {
            $this->dispatch('alert', type: 'warning', message: 'Cart is empty');
            return;
        }

        $order = DB::transaction(function () use ($cart, $user) {

            $subtotal = 0;
            $orderItems = [];

            foreach ($cart->cartItems as $item) {

                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                if ($product->stock_quantity < $item->quantity) {
                    throw new \Exception("Produk {$product->name} out of stock");
                }

                $product->decrement(
                    'stock_quantity',
                    $item->quantity
                );

                $lineSubtotal = $item->price * $item->quantity;
                $subtotal += $lineSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'product_price' => $product->price,
                    'quantity' => $item->quantity,
                    'weight_grams' => $product->weight_grams,
                    'stock_status' => $product->stock_status,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $shippingCost = 0;

            $order = Order::create([
                'user_id' => $user->id,
                'invoice_no' => 'INV-' . date('Ymd') . '-' . Str::random(6),
                'buyer_name' => $this->buyer_name,
                'buyer_email' => $user->email,
                'buyer_whatsapp' => $this->buyer_whatsapp,
                'shipping_address_id' => $this->shipping_address_id,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $subtotal + $shippingCost,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'delivery_status' => 'pending',
                'fulfillment_status' => 'pending',
            ]);

            $order->items()->createMany($orderItems);
            $cart->delete();
            return $order;
        });

        $this->dispatch('cartUpdated');
        $this->dispatch('alert', type: 'success', message: 'Order placed successfully');
        return redirect()->route('orders.detail', $order->invoice_no);
    }

    public function render()
    {
        return view('livewire.checkout', [
            'cart' => $this->cart,
        ]);
    }
}