<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderDetail extends Component
{
    public $invoice_no;

    public function mount($invoice_no)
    {
        $this->invoice_no = $invoice_no;
    }

    public function getOrderProperty()
    {
        return Order::with(['items.product', 'shipments', 'payments', 'shippingAddress'])->where('invoice_no', $this->invoice_no)->where('user_id', Auth::id())->firstorFail();
    }

    public function render()
    {
        return view('livewire.order-detail', [
            'order' => $this->order
        ]);
    }
}
