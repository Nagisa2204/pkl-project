<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Services\OrderLifecycleService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class AdminOrderDetail extends Component
{
    public string $invoice_no;
    public string $shipment_status = 'pending';
    public string $awb_number = '';

    public function mount(string $invoice_no): void
    {
        $this->authorize('admin');
        $this->invoice_no = $invoice_no;
        $shipment = $this->order->shipments->first();
        $this->shipment_status = $shipment?->status ?? 'pending';
        $this->awb_number = $shipment?->awb_number ?? '';
    }

    public function getOrderProperty(): Order
    {
        return Order::with(['user', 'items', 'payments', 'shipments', 'shippingAddress', 'invoice'])
            ->where('invoice_no', $this->invoice_no)->firstOrFail();
    }

    public function updateShipment(OrderLifecycleService $lifecycle): void
    {
        $this->authorize('admin');
        abort_unless($this->order->payment_status === 'paid', 422, 'Pesanan belum dibayar.');
        $this->validate(['shipment_status' => ['required', 'in:pending,processing,shipped,delivered'], 'awb_number' => ['nullable', 'string', 'max:255']]);
        $lifecycle->updateShipment($this->order, $this->shipment_status, $this->awb_number ?: null);
        unset($this->order);
        session()->flash('success', 'Status pengiriman berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.admin.admin-order-detail', ['order' => $this->order]);
    }
}
