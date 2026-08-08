<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 text-2xl font-bold text-gray-900">
        Riwayat Pesanan
    </h1>

    <table class="w-full border-collapse text-center bg-white">
        <thead class="bg-gray-50">
            <tr>
                <th class="py-3 px-2 border-b-2 border-gray-200">No. Pesanan</th>
                <th class="py-3 px-2 border-b-2 border-gray-200">Tanggal</th>
                <th class="py-3 px-2 border-b-2 border-gray-200">Total</th>
                <th class="py-3 px-2 border-b-2 border-gray-200">Status</th>
                <th class="py-3 px-2 border-b-2 border-gray-200">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="py-3 px-2 border-b border-gray-200">{{ $order->order_no }}</td>
                    <td class="py-3 px-2 border-b border-gray-200">{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td class="py-3 px-2 border-b border-gray-200">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td class="py-3 px-2 border-b border-gray-200 font-bold">
                        @php($statusLabels = ['pending_payment' => 'Menunggu Pembayaran', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'])
                        <span class="{{ in_array($order->status, ['cancelled'], true) ? 'text-red-500' : ($order->status === 'completed' ? 'text-green-600' : 'text-indigo-600') }}">{{ $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                    </td>
                    <td class="py-3 px-2 border-b border-gray-200">
                        <a href="{{ route('orders.detail', $order->invoice_no) }}" class="text-blue-500 font-bold hover:underline">
                            Lihat Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center p-5">Tidak ada riwayat pesanan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-5">{{ $orders->links() }}</div>
</div>
