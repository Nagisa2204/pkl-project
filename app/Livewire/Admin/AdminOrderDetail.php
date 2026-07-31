<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;

#[Layout('layouts.admin')]
class AdminOrderDetail extends Component
{
    public $invoice_no;

    public function mount($invoice_no)
    {
        $this->invoice_no = $invoice_no;
    }

    public function getOrderProperty()
    {
        return Order::with([
            'user',
            'items.product',
            'payments',
            'shipments',
            'shippingAddress'
        ])
        ->where('invoice_no', $this->invoice_no)
        ->firstOrFail();
    }

    public function updateStatus($status)
    {
        $this->order->update(['status' => $status]);

        session()->flash('success', 'Order status updated successfully');
    }

    public function render()
    {
        return view('livewire.admin.admin-order-detail', [
            'order' => $this->order
        ]);
    }
}
