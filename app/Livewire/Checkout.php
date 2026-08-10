<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\UserAddress;
use App\Services\CreateOrderService;
use App\Services\ShippingRateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use RuntimeException;

class Checkout extends Component
{
    public string $buyer_name = '';
    public string $buyer_whatsapp = '';
    public ?int $shipping_address_id = null;
    public array $selected_cart_items = [];
    public bool $has_address = false;
    public int $total_weight = 0;
    public string $courier = '';
    public array $services = [];
    public string $shipping_service = '';
    public ?int $shipping_cost = null;
    public bool $orderCreated = false;

    protected function rules(): array
    {
        return [
            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_whatsapp' => ['required', 'string', 'max:30'],
            'shipping_address_id' => [
                'required',
                'integer',
                Rule::exists('user_addresses', 'id')->where('user_id', Auth::id()),
            ],
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
        $this->resetShipping();
    }

    public function calculateTotalWeight(): void
    {
        $this->total_weight = (int) ($this->cart?->cartItems->sum(
            fn ($item) => max(1, $item->variant->weight_grams) * $item->quantity
        ) ?? 0);
    }

    public function updatedCourier(string $value, ShippingRateService $shipping): void
    {
        $this->shipping_cost = null;
        $this->shipping_service = '';
        $this->services = [];

        $address = $this->selectedAddress();

        if (! $value || ! $address) {
            return;
        }

        try {
            $this->services = $shipping->rates($address, max(1, $this->total_weight), $value);
        } catch (\Throwable $exception) {
            report($exception);
            $this->addError('shipping', $exception->getMessage());
        }
    }

    public function updatedShippingService(string $value): void
    {
        $rate = collect($this->services)->firstWhere('service', $value);
        $this->shipping_cost = $rate ? (int) ($rate['cost'] ?? 0) : null;
    }

    public function placeOrder(ShippingRateService $shipping, CreateOrderService $orders): mixed
    {
        if ($this->orderCreated) {
            return null;
        }

        $this->validate();

        $address = $this->selectedAddress();

        if (! $address || ! $this->cart?->cartItems->count()) {
            $this->addError('cart', 'Keranjang atau alamat pengiriman tidak valid.');
            return null;
        }

        try {
            $quote = $shipping->authoritativeRate(
                $address,
                max(1, $this->total_weight),
                $this->courier,
                $this->shipping_service
            );

            if ($this->shipping_cost === null || $quote->cost() !== $this->shipping_cost) {
                $this->shipping_cost = $quote->cost();
                $this->addError('shipping_service', 'Biaya pengiriman berubah. Periksa total terbaru, lalu lanjutkan kembali.');

                return null;
            }

            $order = $orders->create(
                Auth::user(),
                $this->selected_cart_items,
                $this->shipping_address_id,
                $quote,
                $this->courier,
                $this->buyer_name,
                $this->buyer_whatsapp
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (RuntimeException $exception) {
            report($exception);
            $this->addError('checkout', $exception->getMessage());
            return null;
        } catch (\Throwable $exception) {
            report($exception);
            $this->addError('checkout', 'Pesanan belum dapat dibuat. Silakan coba kembali.');
            return null;
        }

        session()->forget('selected_cart_items');
        $this->orderCreated = true;
        $this->dispatch('cartUpdated');

        $payment = $order->payments->firstWhere('provider', 'midtrans');
        $redirectUrl = route('orders.detail', $order->invoice_no);

        if (! $payment?->snap_token) {
            session()->flash('error', 'Pesanan berhasil dibuat, tetapi pembayaran belum dapat dimuat. Silakan coba kembali dari detail pesanan.');

            return redirect()->to($redirectUrl);
        }

        $this->dispatch(
            'midtrans-snap-open',
            token: $payment->snap_token,
            redirectUrl: $redirectUrl,
        );

        return null;
    }

    private function resetShipping(): void
    {
        $this->courier = '';
        $this->services = [];
        $this->shipping_service = '';
        $this->shipping_cost = null;
    }

    private function selectedAddress(): ?UserAddress
    {
        if (! $this->shipping_address_id) {
            return null;
        }

        return Auth::user()->userAddresses()->find($this->shipping_address_id);
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
