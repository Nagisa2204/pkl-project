<x-app-layout>
    <div class="bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6 py-10">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <p class="uppercase tracking-[4px] text-indigo-600 text-sm font-bold">
                        Katalog
                    </p>
                    <h1 class="text-5xl font-extrabold text-slate-900 mt-2">
                        Katalog Produk & Jasa
                    </h1>
                    <p class="mt-4 text-gray-500 max-w-2xl">
                        Cari produk berdasarkan kata kunci atau pilih kategori untuk menemukan
                        produk atau jasa yang tepat.
                    </p>
                </div>
                <a
                    href="{{ route('cart') }}"
                    class="bg-slate-900 text-white rounded-2xl px-6 py-3 flex items-center gap-2 shadow">
                    🛒 Lihat Keranjang
                </a>
            </div>
            <!-- Search -->
            <div class="bg-white rounded-3xl shadow p-5 mt-10">
                <div class="grid md:grid-cols-12 gap-4">
                    <div class="md:col-span-7">
                        <input
                            type="text"
                            wire:model.live.debounce.500ms="search"
                            placeholder="Cari produk, jasa, peta, atau aset GIS..."
                            class="w-full rounded-2xl border-gray-200 h-14">
                    </div>
                    <div class="md:col-span-3">
                        <select
                            wire:model.live="selectedCategory"
                            class="w-full rounded-2xl border-gray-200 h-14">
                            <option value="">
                                Semua kategori
                            </option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <button
                            wire:click="$refresh"
                            class="w-full h-14 rounded-2xl border bg-white font-semibold hover:bg-gray-50">
                            Atur Ulang
                        </button>
                    </div>
                </div>
            </div>
            <!-- Produk -->
            <div class="grid lg:grid-cols-4 md:grid-cols-2 gap-8 mt-10">
                @forelse($products as $product)
                    <div
                        class="bg-white rounded-[28px] overflow-hidden shadow hover:shadow-xl duration-300">
                        <!-- Gambar -->
                        <img
                            src="{{ $product->images->first()?->image_url ?? asset('images/no-image.png') }}"
                            class="w-full h-64 object-cover">
                        <!-- Body -->
                        <div class="p-5">
                            <div class="flex gap-2 flex-wrap">
                                <span class="bg-indigo-100 text-indigo-700 text-xs rounded-full px-3 py-1">
                                    {{ $product->category->name }}
                                </span>
                                <span class="bg-gray-100 text-gray-700 text-xs rounded-full px-3 py-1">
                                    {{ ucfirst($product->type) }}
                                </span>
                            </div>
                            <h2 class="font-bold text-2xl mt-4">
                                {{ $product->name }}
                            </h2>
                            <p class="text-indigo-600 font-extrabold text-4xl mt-4">
                                Rp{{ number_format($product->price,0,',','.') }}
                            </p>
                            @if($product->stock_quantity > 0)
                                <p class="text-green-600 font-semibold text-sm mt-1">
                                    Stok tersedia
                                </p>
                            @else
                                <p class="text-red-500 font-semibold text-sm mt-1">
                                    Stok habis
                                </p>
                            @endif
                            <div class="grid grid-cols-2 gap-3 mt-6">
                                <a
                                    href="{{ route('products.show',$product->id) }}"
                                    class="border rounded-2xl py-3 text-center font-semibold hover:bg-gray-100">
                                    Detail
                                </a>
                                <button
                                    wire:click="addToCart({{ $product->id }})"
                                    class="rounded-2xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                                    Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-16">
                        <h3 class="text-2xl font-bold text-gray-500">
                            Produk tidak ditemukan
                        </h3>
                    </div>
                @endforelse
            </div>
            <!-- Pagination -->
            <div class="mt-10">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>