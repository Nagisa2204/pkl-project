<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">        
    <div x-data="{ show: false, message: '', type: 'success' }" 
        x-on:alert.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
        x-show="show" 
        x-transition
        x-cloak
        class="fixed bottom-5 right-5 z-50 p-4 rounded-2xl shadow-xl text-white font-medium text-sm flex items-center gap-2"
        :class="type === 'warning' ? 'bg-amber-500' : (type === 'error' ? 'bg-rose-600' : 'bg-emerald-600')">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
        </svg>
        <span x-text="message"></span>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Katalog Produk</h1>
            <p class="text-sm text-gray-500">Temukan berbagai produk berkualitas yang Anda butuhkan</p>
        </div>

        <div class="relative w-full md:w-80">
            <input type="text" 
                    wire:model.live.debounce.300ms="search" 
                   placeholder="Cari nama produk..." 
                   class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-200 text-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6 no-scrollbar">
        <button wire:click="$set('selectedCategory', null)" 
                class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap border transition-all
                {{ is_null($selectedCategory) ? 'bg-black text-white border-black' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
            Semua Kategori
        </button>

        @foreach($categories as $category)
            <button wire:click="$set('selectedCategory', {{ $category->id }})" 
                    class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap border transition-all
                    {{ $selectedCategory == $category->id ? 'bg-black text-white border-black' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
        @forelse($products as $product)
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-200 flex flex-col justify-between group">
                <div>
                    <a href="{{ route('product.detail', $product->slug) }}" wire:navigate class="block relative aspect-square bg-gray-50 overflow-hidden">
                        @if($product->images && $product->images->first())
                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                alt="{{ $product->name }}" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                                No Image
                            </div>
                        @endif

                        <span class="absolute top-2 left-2 bg-white/90 backdrop-blur-md px-2.5 py-0.5 rounded-full text-[10px] font-semibold text-gray-800 border border-gray-100">
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </span>
                    </a>

                    <div class="p-4">
                        <a href="{{ route('product.detail', $product->slug) }}" wire:navigate class="block font-semibold text-gray-900 text-sm hover:text-gray-600 line-clamp-2 mb-2">
                            {{ $product->name }}
                        </a>
                        <p class="font-bold text-gray-900 text-base">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Stok: {{ $product->stock_quantity }}
                        </p>
                    </div>
                </div>

                <div class="p-4 pt-0">
                    <button wire:click="addToCart({{ $product->id }})" 
                        wire:loading.attr="disabled"
                        class="w-full py-2 px-3 bg-black hover:bg-gray-800 disabled:bg-gray-300 text-white rounded-full font-semibold text-xs transition-all flex items-center justify-center gap-1.5 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>

                        <span wire:loading.remove wire:target="addToCart({{ $product->id }})">Keranjang</span>
                        <span wire:loading wire:target="addToCart({{ $product->id }})">Memproses...</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-300 mx-auto mb-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <p class="text-gray-500 text-sm font-medium">Tidak ada produk yang ditemukan</p>
                <p class="text-xs text-gray-400 mt-1">Coba gunakan kata kunci lain atau pilih kategori berbeda.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>