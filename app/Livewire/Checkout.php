<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Services\CreateOrderService;
use App\Services\RajaOngkirService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Checkout extends Component
{
    public string $buyer_name = '';
    public string $buyer_whatsapp = '';
    public ?int $shipping_address_id = null;
    public array $selected_cart_items = [];
    public bool $has_address = false;
    public string $selected_bank = '';
    public ?int $destination_id = null;
    public int $total_weight = 0;
    public string $courier = '';
    public array $services = [];
    public string $shipping_service = '';
    public ?int $shipping_cost = null;

    protected function rules(): array
    {
        return [
            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_whatsapp' => ['required', 'string', 'max:30'],
            'shipping_address_id' => ['required', 'integer'],
            'selected_bank' => ['required', Rule::in(['bca_va', 'bni_va', 'bri_va', 'echannel', 'qris', 'all'])],
            'courier' => ['required', Rule::in(config('rajaongkir.default_couriers', []))],
            'shipping_service' => ['required', 'string', 'max:80'],
        ];
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->selected_cart_items = session('selected_cart_items', []);

        if (empty($this->selected_cart_items)) {
            $this->redirectRoute('cart');
            return;
        }

        $this->buyer_name = $user->name;
        $this->buyer_whatsapp = $user->phone ?? '';
        $address = $user->userAddresses()->where('is_default', true)->first()
            ?? $user->userAddresses()->first();
        $this->has_address = $address !== null;
        $this->shipping_address_id = $address?->id;
        $this->destination_id = $address?->destination_id;
        $this->calculateTotalWeight();
    }

    public function getCartProperty(): ?Cart
    {
        return Cart::with(['cartItems' => fn ($query) => $query
            ->whereIn('id', array_map('intval', $this->selected_cart_items))
            ->with(['variant.product', 'variant.optionValues.option'])])
            ->where('user_id', Auth::id())
            ->first();
    }

    public function getAddressesProperty()
    {
        return Auth::user()->userAddresses()->orderByDesc('is_default')->get();
    }

    public function updatedShippingAddressId($value): void
    {
        $address = Auth::user()->userAddresses()->find($value);
        $this->destination_id = $address?->destination_id;
        $this->resetShipping();
    }

    public function calculateTotalWeight(): void
    {
        $this->total_weight = (int) ($this->cart?->cartItems->sum(
            fn ($item) => max(1, $item->variant->weight_grams) * $item->quantity
        ) ?? 0);
    }

    public function updatedCourier(string $value, RajaOngkirService $shipping): void
    {
        $this->shipping_cost = null;
        $this->shipping_service = '';
        $this->services = [];

        if (! $value || ! $this->destination_id) {
            return;
        }

        try {
            $this->services = $shipping->rates($this->destination_id, max(1, $this->total_weight), $value);
        } catch (\Throwable $exception) {
            report($exception);
            $this->addError('courier', 'Layanan ongkir sedang tidak tersedia. Silakan coba lagi.');
        }
    }

    public function updatedShippingService(string $value): void
    {
        $rate = collect($this->services)->firstWhere('service', $value);
        $this->shipping_cost = $rate ? (int) ($rate['cost'] ?? 0) : null;
    }

    public function placeOrder(RajaOngkirService $shipping, CreateOrderService $orders): mixed
    {
        $this->validate();

        if (! $this->destination_id || ! $this->cart?->cartItems->count()) {
            $this->addError('cart', 'Keranjang atau alamat pengiriman tidak valid.');
            return null;
        }

        try {
            $rate = $shipping->authoritativeRate(
                $this->destination_id,
                max(1, $this->total_weight),
                $this->courier,
                $this->shipping_service
            );

            $order = $orders->create(
                Auth::user(),
                $this->selected_cart_items,
                $this->shipping_address_id,
                $rate,
                $this->courier,
                $this->selected_bank,
                $this->buyer_name,
                $this->buyer_whatsapp
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);
            $this->addError('checkout', 'Pesanan belum dapat dibuat. Silakan coba kembali.');
            return null;
        }

        session()->forget('selected_cart_items');
        $this->dispatch('cartUpdated');

        return redirect()->route('orders.detail', $order->invoice_no);
    }

    private function resetShipping(): void
    {
        $this->courier = '';
        $this->services = [];
        $this->shipping_service = '';
        $this->shipping_cost = null;
    }

    public function render()
    {
        return view('livewire.checkout', [
            'cart' => $this->cart,
            'addresses' => $this->addresses,
            'couriers' => config('rajaongkir.default_couriers', []),
        ]);
    }
}
