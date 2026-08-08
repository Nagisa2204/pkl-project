<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">
            Order History
        </h1>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full border-collapse text-center bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-2 border-b-2 border-gray-200">
                        Order ID
                    </th>
                    <th class="py-3 px-2 border-b-2 border-gray-200">
                        Customer
                    </th>
                    <th class="py-3 px-2 border-b-2 border-gray-200">
                        Total Amount
                    </th>
                    <th class="py-3 px-2 border-b-2 border-gray-200">
                        Status
                    </th>
                    <th class="py-3 px-2 border-b-2 border-gray-200">
                        Order Date
                    </th>
                    <th class="py-3 px-2 border-b-2 border-gray-200">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody">
                @forelse($orders as $order)
                    <tr>
                        <td class="py-3 px-2 border-b border-gray-200">
                            {{ $order->id }}
                        </td>
                        <td class="py-3 px-2 border-b border-gray-200">
                            {{ $order->user->name }}
                        </td>
                        <td class="py-3 px-2 border-b border-gray-200">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-2 border-b border-gray-200">
                            {{ ucfirst($order->status) }}
                        </td>
                        <td class="py-3 px-2 border-b border-gray-200">
                            {{ $order->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="py-3 px-2 border-b border-gray-200">
                            <a href="{{ route('admin.orders.detail', $order->id) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-gray-500">
                            No orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
