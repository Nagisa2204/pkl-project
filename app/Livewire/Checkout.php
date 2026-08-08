<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;
use Midtrans\Config;
use Midtrans\Snap;

class Checkout extends Component
{
    public $buyer_name;
    public $buyer_whatsapp;
    public $shipping_address_id;
    public $selected_cart_items = [];

    public $has_address = false;

    public $selected_bank = '';

    public $destination_id;
    public $total_weight = 0;
    public $courier = '';
    public $services = [];
    public $shipping_service = '';
    public $shipping_cost = 0;

    protected function rules(): array
    {
        return [
            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_whatsapp' => ['required', 'string', 'max:255'],
            'selected_bank' => ['required', 'string'],
            'courier' => ['required', 'string'],
            'shipping_service' => ['required', 'string'],
        ];
    }

    public function mount(): void
    {
        $user = Auth::user();

        $this->selected_cart_items = session()->get(
            'selected_cart_items',
            []
        );

        if (empty($this->selected_cart_items)) {
            $this->dispatch(
                'alert',
                type: 'warning',
                message: 'Tidak ada produk yang dipilih untuk dicheckout.'
            );

            $this->redirectRoute('cart.index');

            return;
        }

        $this->buyer_name = $user->name;
        $this->buyer_whatsapp = $user->phone;

        $address = $user->userAddresses()
            ->where('is_default', true)
            ->first();

        if ($address) {
            $this->has_address = true;

            $this->shipping_address_id = $address->id;

            $this->destination_id = $address->destination_id;
        } else {
            $this->has_address = false;

            $this->shipping_address_id = null;
            $this->destination_id = null;
        }

        $this->calculateTotalWeight();
    }

    public function getCartProperty(): ?Cart
    {
        return Cart::with([
            'cartItems' => function ($query) {
                $query
                    ->whereIn('id', $this->selected_cart_items)
                    ->with('product');
            }
        ])
        ->where('user_id', Auth::id())
        ->first();
    }

    public function calculateTotalWeight(): void
    {
        $cart = $this->cart;

        if ($cart && $cart->cartItems) {
            $this->total_weight = $cart->cartItems->sum(
                function ($item) {
                    $weight = $item->product->weight_grams ?? 1000;

                    return $weight * $item->quantity;
                }
            );
        }
    }

    public function updatedCourier($value): void
    {
        $this->shipping_cost = 0;
        $this->shipping_service = '';
        $this->services = [];

        if (!$value) {
            return;
        }

        if (!$this->destination_id) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: 'Destination alamat belum tersedia.'
            );

            return;
        }

        $weight = $this->total_weight > 0
            ? $this->total_weight
            : 1000;

        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])
        ->asForm()
        ->timeout(30)
        ->post(
            'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost',
            [
                'origin' => env('RAJAONGKIR_ORIGIN_CITY_ID'),
                'destination' => $this->destination_id,
                'weight' => $weight,
                'courier' => $value,
            ]
        );

        if ($response->successful()) {
            $data = $response->json();

            $this->services = $data['data'] ?? [];

            if (empty($this->services)) {
                $this->dispatch(
                    'alert',
                    type: 'warning',
                    message: 'Tidak ada layanan pengiriman yang tersedia untuk rute ini.'
                );
            }

            return;
        }

        $this->services = [];

        $this->dispatch(
            'alert',
            type: 'error',
            message: 'Gagal mengambil data ongkos kirim dari RajaOngkir.'
        );
    }

    public function updatedShippingService($value): void
    {
        $this->shipping_cost = 0;

        if (!$value) {
            return;
        }

        $selectedService = collect($this->services)
            ->firstWhere('service', $value);

        if ($selectedService) {
            $this->shipping_cost = (int) (
                $selectedService['cost'] ?? 0
            );
        }
    }

    public function placeOrder()
    {
        if (!$this->has_address) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: 'Silakan isi alamat pengiriman terlebih dahulu!'
            );

            return;
        }

        if (empty($this->destination_id)) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: 'Destination alamat tidak ditemukan.'
            );

            return;
        }

        if (empty($this->selected_bank)) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: 'Silakan pilih metode pembayaran.'
            );

            return;
        }

        if (empty($this->courier)) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: 'Silakan pilih kurir pengiriman.'
            );

            return;
        }

        if (empty($this->shipping_service)) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: 'Silakan pilih layanan pengiriman.'
            );

            return;
        }

        if ($this->shipping_cost <= 0) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: 'Biaya pengiriman belum tersedia.'
            );

            return;
        }

        $this->validate();

        $user = Auth::user();
        $cart = $this->cart;

        $order = DB::transaction(
            function () use ($cart, $user) {

                $subtotal = 0;
                $orderItems = [];

                foreach ($cart->cartItems as $item) {

                    $product = Product::lockForUpdate()
                        ->findOrFail($item->product_id);

                    if ($product->stock_quantity < $item->quantity) {
                        throw new \Exception(
                            "Produk {$product->name} out of stock"
                        );
                    }

                    $product->decrement(
                        'stock_quantity',
                        $item->quantity
                    );

                    $lineSubtotal =
                        $item->price * $item->quantity;

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

                $invoiceNo =
                    'INV-' .
                    date('Ymd') .
                    '-' .
                    Str::random(6);

                $grandTotal =
                    $subtotal + $this->shipping_cost;
                
                $selectedService = collect($this->services)
                    ->firstWhere('service', $this->shipping_service);

                $order = Order::create([
                    'user_id' => $user->id,
                    'invoice_no' => $invoiceNo,

                    'buyer_name' => $this->buyer_name,
                    'buyer_email' => $user->email,
                    'buyer_whatsapp' => $this->buyer_whatsapp,

                    'shipping_address_id' => $this->shipping_address_id,

                    'subtotal' => $subtotal,
                    'shipping_cost' => $this->shipping_cost,

                    'shipping_courier_code' => $this->courier,
                    'shipping_courier_name' => strtoupper($this->courier),
                    'shipping_service_code' => $this->shipping_service,
                    'shipping_service_name' => $selectedService['description'] ?? '',
                    'shipping_etd' => $selectedService['etd'] ?? '',

                    'total' => $grandTotal,

                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'delivery_status' => 'pending',
                    'fulfillment_status' => 'pending',
                ]);

                $order->items()->createMany($orderItems);

                CartItem::whereIn(
                    'id',
                    $this->selected_cart_items
                )->delete();

                session()->forget('selected_cart_items');

                Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
                Config::$isSanitized = true;
                Config::$is3ds = true;

                $params = [
                    'transaction_details' => [
                        'order_id' => $order->invoice_no,
                        'gross_amount' => $order->total,
                    ],

                    'customer_details' => [
                        'first_name' => $order->buyer_name,
                        'email' => $order->buyer_email,
                        'phone' => $order->buyer_whatsapp,
                    ],

                    'expiry' => [
                        'start_time' => now()->format('Y-m-d H:i:s O'),
                        'unit' => 'minutes',
                        'duration' => 60,
                    ],
                ];

                if ($this->selected_bank !== 'all') {
                    $params['enabled_payments'] = [
                        $this->selected_bank
                    ];
                }

                $snapToken =
                    Snap::getSnapToken($params);

                Payment::create([
                    'order_id' => $order->id,
                    'provider' => 'midtrans',
                    'bank' => $this->selected_bank,
                    'payment_type' => $this->selected_bank,
                    'status' => 'pending',
                    'gross_amount' => $order->total,
                    'snap_token' => $snapToken,
                ]);

                return $order;
            }
        );

        $this->dispatch('cartUpdated');

        $this->dispatch(
            'alert',
            type: 'success',
            message: 'Pesanan berhasil dibuat!'
        );

        return redirect()->route(
            'orders.detail',
            $order->invoice_no
        );
    }

    public function render()
    {
        return view('livewire.checkout', [
            'cart' => $this->cart,
        ]);
    }
}