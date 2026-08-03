<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">
            Product Management
        </h1>
    </div>

    <div class="flex items-center justify-between gap-4">
        <select wire:model="selectedCategory" class="w-64 rounded-lg border border-gray-300 p-2 text-sm focus:border-blue-500 focus:outline-none">
            <option value="">Produk Fisik</option>
        </select>
        <a href="{{ route('admin.products.create') }}" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
            Tambah Product
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full border-collapse text-center bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-2 border-b-2 border-gray-200">Product</th>
                    <th class="py-3 px-2 border-b-2 border-gray-200">SKU</th>
                    <th class="py-3 px-2 border-b-2 border-gray-200">Category</th>
                    <th class="py-3 px-2 border-b-2 border-gray-200">Price</th>
                    <th class="py-3 px-2 border-b-2 border-gray-200">Stock</th>
                    <th class="py-3 px-2 border-b-2 border-gray-200">Status</th>
                    <th class="py-3 px-2 border-b-2 border-gray-200">Action</th>
                </tr>
            </thead>
            <tbody">
                @forelse($products as $product)
                    <tr>
                        <td class="py-3 px-2 border-b border-gray-200">{{ $product->name }}</td>
                        <td class="py-3 px-2 border-b border-gray-200">{{ $product->sku }}</td>
                        <td class="py-3 px-2 border-b border-gray-200">{{ $product->category->name }}</td>
                        <td class="py-3 px-2 border-b border-gray-200">Rp {{ number_format($product->price,0,',','.') }}</td>
                        <td class="py-3 px-2 border-b border-gray-200">{{ $product->stock_quantity }}</td>
                        <td class="py-3 px-2 border-b border-gray-200">
                            @if($product->is_active)
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Active</span>
                            @else
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Inactive</span>
                            @endif
                        </td>

                        <td class="py-3 px-2 border-b border-gray-200">
                            <a href="{{ route('admin.products.manage', $product->id) }}" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-900">
                                Manage
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-gray-500">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $products->links() }}
    </div>
</div>