<?php

namespace App\Livewire;

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

    public function retryPayment(MidtransService $midtrans): void
    {
        $order = $this->order;
        abort_unless($order->payment_status === 'pending', 422);
        $token = $midtrans->createSnapToken($order);
        $order->payments()->where('provider', 'midtrans')->update([
            'snap_token' => $token,
            'status' => 'pending',
            'expiry_at' => now()->addMinutes(config('midtrans.expiry_minutes', 60)),
        ]);
        unset($this->order);
    }

    public function render()
    {
        return view('livewire.order-detail', [
            'order' => $this->order
        ]);
    }
}
