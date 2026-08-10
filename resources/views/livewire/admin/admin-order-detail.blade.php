<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-content">{{ $order->order_no }}</h1>
            <p class="mt-1 text-sm text-muted">Invoice {{ $order->invoice_no }}</p>
        </div>
        <x-ui.button :href="route('admin.orders')" variant="outline">Kembali</x-ui.button>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['label' => 'Pesanan', 'status' => $order->status],
            ['label' => 'Pembayaran', 'status' => $order->payment_status],
            ['label' => 'Pengiriman', 'status' => $order->delivery_status],
            ['label' => 'Pemenuhan', 'status' => $order->fulfillment_status],
        ] as $item)
            <x-ui.card>
                <span class="text-xs font-semibold uppercase tracking-wide text-muted">{{ $item['label'] }}</span>
                <div class="mt-2">
                    <x-ui.badge :variant="$item['status']->badgeVariant()">{{ $item['status']->label() }}</x-ui.badge>
                </div>
            </x-ui.card>
        @endforeach
    </div>

    <x-ui.card :padding="false">
        <div class="ui-card-header">
            <h2 class="font-bold text-content">Item Pesanan</h2>
        </div>
        <div class="ui-table-wrap rounded-none border-0">
            <table class="ui-table min-w-[700px]">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>SKU</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td class="font-semibold text-content">
                                {{ $item->product_name }}
                                <small class="mt-1 block font-normal text-muted">{{ $item->variant_name }}</small>
                            </td>
                            <td>{{ $item->sku }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="ui-card-footer text-right text-lg font-bold">Total Rp {{ number_format($order->total, 0, ',', '.') }}</div>
    </x-ui.card>

    @php($shipment = $order->shipments->first())
    <x-ui.card>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="font-bold text-content">Pengiriman</h2>
                <p class="mt-2 text-sm leading-6 text-muted">
                    {{ $shipment?->receiver_name }} · {{ $shipment?->phone }}<br>
                    {{ $shipment?->address_line }}, {{ $shipment?->destination_label }} {{ $shipment?->postal_code }}<br>
                    {{ $shipment?->courier_name }} {{ $shipment?->service_name }}
                </p>
            </div>
            @if($shipment?->origin_address)
                <div class="max-w-md rounded-ui bg-subtle p-3 text-sm text-muted">
                    <strong class="text-content">Pickup dari toko</strong>
                    <span class="mt-1 block">{{ $shipment->origin_address }}</span>
                </div>
            @endif
        </div>

        <div class="mt-5 grid gap-4 md:grid-cols-[minmax(180px,1fr)_minmax(180px,1fr)_auto] md:items-end">
            <div>
                <label class="ui-field-label" for="awb-number">Nomor resi</label>
                <input id="awb-number" wire:model="awb_number" placeholder="Masukkan nomor resi" class="ui-field">
                <x-input-error :messages="$errors->get('awb_number')" />
            </div>
            <x-ui.searchable-select
                wire:model="shipment_status"
                :options="$shipmentOptions"
                label="Status pengiriman"
                :clearable="false"
            />
            <x-ui.button
                wire:click="updateShipment"
                wire:loading.attr="disabled"
                wire:target="updateShipment"
                :disabled="$order->payment_status !== \App\Enums\PaymentStatus::Paid"
            >
                <span wire:loading.remove wire:target="updateShipment">Perbarui Status</span>
                <span wire:loading wire:target="updateShipment">Menyimpan...</span>
            </x-ui.button>
        </div>
        @if($order->payment_status !== \App\Enums\PaymentStatus::Paid)
            <p class="mt-3 text-xs text-warning">Status pengiriman baru dapat diubah setelah pembayaran terverifikasi.</p>
        @endif
    </x-ui.card>

    <div class="ui-form-actions">
        <x-ui.button :href="route('invoices.show', $order)" target="_blank" rel="noopener" variant="secondary">Lihat Invoice</x-ui.button>
    </div>
</div>
