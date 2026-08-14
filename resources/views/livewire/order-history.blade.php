<div class="ui-page">
    <div class="mb-6">
        <h1 class="font-extrabold text-primary">Riwayat</h1>
        <h2 class="text-2xl font-extrabold text-content">Riwayat Pesanan</h2>
        <p class="mt-1 text-sm text-muted">Pantau pembelian seluruh pesanan.</p>
    </div>

    <div class="ui-table-wrap mt-6 rounded-xl border border-default">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr wire:key="order-history-{{ $order->id }}">
                        <td class="font-semibold">{{ $order->order_no }}</td>
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td class="text-center"><x-ui.badge :variant="$order->status->badgeVariant()">{{ $order->status->label() }}</x-ui.badge></td>
                        <td class="text-center"><x-ui.button :href="route('orders.detail', $order->invoice_no)" variant="ghost" size="sm">Lihat detail</x-ui.button></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-muted">Tidak ada riwayat pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $orders->links() }}</div>
</div>
