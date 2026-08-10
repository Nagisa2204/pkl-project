<div class="ui-page">
    <h1 class="mb-6 text-2xl font-bold text-content">Riwayat Pesanan</h1>

    <div class="ui-table-wrap">
        <table class="ui-table min-w-[760px]">
            <thead><tr><th>No. Pesanan</th><th>Tanggal</th><th>Total</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
                @forelse($orders as $order)
                    <tr wire:key="order-history-{{ $order->id }}">
                        <td class="font-semibold">{{ $order->order_no }}</td>
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td><x-ui.badge :variant="$order->status->badgeVariant()">{{ $order->status->label() }}</x-ui.badge></td>
                        <td class="text-right"><x-ui.button :href="route('orders.detail', $order->invoice_no)" variant="ghost" size="sm">Lihat detail</x-ui.button></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-10 text-center text-muted">Tidak ada riwayat pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $orders->links() }}</div>
</div>
