<?php

namespace App\Livewire\Admin;

use App\Enums\PaymentStatus;
use App\Enums\ShipmentStatus;
use App\Models\Order;
use App\Services\OrderLifecycleService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\Rule;

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
        $this->shipment_status = $shipment?->status->value ?? ShipmentStatus::Pending->value;
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
        abort_unless($this->order->payment_status === PaymentStatus::Paid, 422, 'Pesanan belum dibayar.');
        $this->validate([
            'shipment_status' => ['required', Rule::enum(ShipmentStatus::class)],
            'awb_number' => ['nullable', 'string', 'max:255'],
        ]);
        $lifecycle->updateShipment($this->order, $this->shipment_status, $this->awb_number ?: null);
        unset($this->order);
        session()->flash('success', 'Status pengiriman berhasil diperbarui.');
        $this->dispatch('toast', variant: 'success', message: 'Status pengiriman berhasil diperbarui.');
    }

    public function render()
    {
        $order = $this->order;
        $currentStatus = $order->shipments->first()?->status ?? ShipmentStatus::Pending;
        $shipmentOptions = collect(ShipmentStatus::cases())
            ->reject(fn (ShipmentStatus $status) => $status === ShipmentStatus::Cancelled)
            ->filter(fn (ShipmentStatus $status) => $currentStatus->canTransitionTo($status))
            ->mapWithKeys(fn (ShipmentStatus $status) => [$status->value => $status->label()])
            ->all();

        return view('livewire.admin.admin-order-detail', [
            'order' => $order,
            'shipmentOptions' => $shipmentOptions,
        ]);
    }
}
