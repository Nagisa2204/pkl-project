<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Product Management
            </h1>
            <p class="text-sm text-gray-500">
                Manage all physical products.
            </p>
        </div>

        <div class="flex items-center gap-6">
            {{-- Sort --}}
            <div class="relative">
                <select wire:model="selectedCategory" class="appearance-none rounded-lg border border-gray-300 bg-white pl-4 pr-10 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Produk Fisik</option>
                </select>
            </div>

            {{-- Add --}}
            <a href="{{ route('admin.products.create') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                Add +
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <table class="w-full divide-y divide-gray-200">

            <thead class="border border-black bg-gray-50">
                <tr>

                    <th class="border border-black px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Product
                    </th>

                    <th class="border border-black px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Price
                    </th>

                    <th class="border border-black px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Stock
                    </th>

                    <th class="border border-black px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Status
                    </th>

                    <th class="border border-black px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Action
                    </th>

                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 bg-white">

                @forelse($products as $product)

                    <tr class="hover:bg-gray-50">

                        {{-- Product --}}
                        <td class="border border-black px-6 py-4">
                            <div class="font-medium text-gray-800">
                                {{ $product->name }}
                            </div>

                            <div class="text-sm text-gray-500">
                                {{ $product->category->name ?? 'Produk Fisik' }}
                            </div>
                        </td>

                        {{-- Price --}}
                        <td class="border border-black px-6 py-4">
                            Rp {{ number_format($product->price,0,',','.') }}
                        </td>

                        {{-- Stock --}}
                        <td class="border border-black px-6 py-4">
                            {{ $product->stock }}
                        </td>

                        {{-- Status --}}
                        <td class="border border-black px-6 py-4">

                            @if($product->is_active)
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Active
                                </span>
                            @else
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Inactive
                                </span>
                            @endif

                        </td>

                        {{-- Action --}}
                        <td class="border border-black px-6 py-4 text-center">
                            <a href="{{ route('admin.products.manage', $product->id) }}" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-900">
                                Manage
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="py-10 text-center text-gray-500">
                            No products found.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- Pagination --}}
    <div>
        {{ $products->links() }}
    </div>

</div>