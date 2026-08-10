<?php

namespace App\Livewire;

use App\Enums\PaymentStatus;
use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Services\MidtransService;

class OrderDetail extends Component
{
    public $invoice_no;

    public function mount($invoice_no)
    {
        $this->invoice_no = $invoice_no;
    }

    public function getOrderProperty()
    {
        return Order::with(['items', 'shipments', 'payments', 'shippingAddress', 'invoice'])->where('invoice_no', $this->invoice_no)->where('user_id', Auth::id())->firstOrFail();
    }

    public function openPayment(MidtransService $midtrans): void
    {
        $order = $this->order;
        abort_unless($order->payment_status === PaymentStatus::Pending, 422);
        $payment = $order->payments->firstWhere('provider', 'midtrans');

        try {
            $token = $payment?->snap_token;

            if (! $token) {
                $token = $midtrans->createSnapToken($order);
                $order->payments()->where('provider', 'midtrans')->update([
                    'snap_token' => $token,
                    'status' => 'pending',
                    'expiry_at' => now()->addMinutes(config('midtrans.expiry_minutes', 60)),
                ]);
            }

            $this->dispatch(
                'midtrans-snap-open',
                token: $token,
                redirectUrl: route('orders.detail', $order->invoice_no),
            );
        } catch (\Throwable $exception) {
            report($exception);
            $this->dispatch('toast', variant: 'danger', message: 'Pembayaran belum dapat dimuat. Silakan coba kembali.');
        }
    }

    public function render()
    {
        return view('livewire.order-detail', [
            'order' => $this->order
        ]);
    }
}
