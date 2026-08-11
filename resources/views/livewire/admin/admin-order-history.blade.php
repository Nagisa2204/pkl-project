<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-content">Riwayat Pesanan</h1>
        <p class="mt-1 text-sm text-muted">Pantau pembayaran dan pemenuhan seluruh pesanan.</p>
    </div>

    <x-ui.card>
        <div class="grid gap-4 md:grid-cols-[minmax(240px,1fr)_280px]">
            <div>
                <label class="ui-field-label" for="order-search">Cari pesanan</label>
                <input id="order-search" wire:model.live.debounce.300ms="search" class="ui-field"
                    placeholder="Nomor invoice atau nama pelanggan">
            </div>
            <x-ui.searchable-select wire:model.live="status" :options="\App\Enums\OrderStatus::options()" label="Status pesanan"
                placeholder="Semua status" search-placeholder="Cari status..." />
        </div>
    </x-ui.card>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Pelanggan</th>
                    <th class="text-right">Total</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr wire:key="admin-order-{{ $order->id }}">
                        <td class="font-semibold">{{ $order->order_no }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td><x-ui.badge :variant="$order->status->badgeVariant()">{{ $order->status->label() }}</x-ui.badge></td>
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <div class="ui-table-actions">
                                <x-ui.button :href="route('admin.orders.detail', $order->invoice_no)" variant="secondary" size="sm">Detail</x-ui.button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-muted">Pesanan belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $orders->links() }}</div>
</div>
