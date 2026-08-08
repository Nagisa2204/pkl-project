<div class="max-w-[1100px] my-10 mx-auto px-5 font-sans bg-slate-50 min-h-screen pt-10">
    <div class="flex flex-wrap bg-white rounded-2xl shadow-sm overflow-hidden">
        
        {{-- Media / Image Container --}}
        <div class="flex-1 min-w-[350px] bg-sky-100 p-5 flex items-center justify-center">
            @if($product->images && $product->images->count() > 0)
                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                     alt="{{ $product->name }}" 
                     class="w-full max-w-[450px] h-auto aspect-square object-cover rounded-2xl shadow-md">
            @else
                <div class="w-full max-w-[450px] aspect-square bg-white rounded-2xl flex items-center justify-center color-[#94a3b8] font-bold text-slate-400">
                    Belum ada gambar
                </div>
            @endif
        </div>

        {{-- Content Info --}}
        <div class="flex-1 min-w-[350px] p-10">
            <div class="flex gap-2.5 mb-5">
                <span class="bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-full text-xs font-bold inline-flex items-center gap-1.25">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                    {{ $product->category->name ?? 'Uncategorized' }}
                </span>
                <span class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-full text-xs font-bold">
                    Produk Fisik
                </span>
            </div>

            <h1 class="m-0 mb-2.5 text-3xl font-extrabold text-slate-900 leading-tight">
                {{ $product->name }}
            </h1>

            <div class="text-3xl font-extrabold text-indigo-600 mb-6">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </div>

            <div class="grid grid-cols-2 gap-3.5 mb-6">
                <div class="bg-slate-50 p-3.5 rounded-xl">
                    <div class="text-slate-400 text-[11px] font-bold uppercase mb-1">Stok Tersedia</div>
                    <div class="text-sm font-bold {{ $product->stock_quantity > 0 ? 'text-slate-900' : 'text-red-600' }}">
                        {{ $product->stock_quantity > 0 ? $product->stock_quantity . ' pcs' : 'Habis' }}
                    </div>
                </div>
                <div class="bg-slate-50 p-3.5 rounded-xl">
                    <div class="text-slate-400 text-[11px] font-bold uppercase mb-1">Berat Produk</div>
                    <div class="text-slate-900 text-sm font-bold">{{ $product->weight_grams ?? 0 }} Gram</div>
                </div>
                <div class="bg-slate-50 p-3.5 rounded-xl">
                    <div class="text-slate-400 text-[11px] font-bold uppercase mb-1">Min. Pembelian</div>
                    <div class="text-slate-900 text-sm font-bold">1 Item</div>
                </div>
                <div class="bg-slate-50 p-3.5 rounded-xl">
                    <div class="text-slate-400 text-[11px] font-bold uppercase mb-1">SKU</div>
                    <div class="text-slate-900 text-sm font-bold">{{ $product->sku ?? '-' }}</div>
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-100 p-5 rounded-xl mb-6">
                <h3 class="m-0 mb-2.5 text-sm font-bold text-slate-700">Deskripsi Produk</h3>
                <div class="leading-relaxed text-slate-500 text-sm">
                    {{ $product->description ?? 'Deskripsi produk belum tersedia. Silakan hubungi admin untuk informasi lebih lanjut mengenai detail dan spesifikasi lengkap produk ini.' }}
                </div>
            </div>

            <button 
                wire:click="addToCart" 
                @if($product->stock_quantity <= 0) disabled @endif
                class="flex items-center justify-center gap-2.5 py-4 px-7 text-base font-bold border-none rounded-xl w-full transition-all duration-200
                       {{ $product->stock_quantity > 0 ? 'cursor-pointer bg-slate-900 text-white hover:-translate-y-0.5 hover:shadow-lg' : 'cursor-not-allowed bg-slate-200 text-slate-400' }}"
            >
                @if($product->stock_quantity > 0)
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    Tambahkan ke Keranjang
                @else
                    Produk Tidak Tersedia
                @endif
            </button>
            
        </div>
    </div>
</div>