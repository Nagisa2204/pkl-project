<div class="mx-auto max-w-7xl px-4 py-8">
    <div class="mb-6 flex flex-col gap-4 rounded-xl bg-white p-5 shadow-sm md:flex-row md:items-end">
        <div class="flex-1">
            <label class="mb-1 block text-sm font-medium text-slate-700">Cari produk</label>
            <input wire:model.live.debounce.350ms="search" type="search" placeholder="Nama produk atau SKU" class="w-full rounded-lg border-slate-300">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Kategori</label>
            <select wire:model.live="selectedCategory" class="rounded-lg border-slate-300">
                <option value="">Semua kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <input wire:model.live.debounce.500ms="minPrice" type="number" min="0" placeholder="Harga min" class="w-32 rounded-lg border-slate-300">
            <input wire:model.live.debounce.500ms="maxPrice" type="number" min="0" placeholder="Harga max" class="w-32 rounded-lg border-slate-300">
        </div>
        <select wire:model.live="sort" class="rounded-lg border-slate-300">
            <option value="latest">Terbaru</option>
            <option value="price_low">Harga terendah</option>
            <option value="price_high">Harga tertinggi</option>
            <option value="name">Nama A-Z</option>
        </select>
    </div>

    <div wire:loading.flex class="mb-4 items-center gap-2 text-sm text-indigo-600">Memuat produk...</div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($products as $product)
            @php
                $variant = $product->defaultVariant ?? $product->activeVariants->first();
                $image = $product->images->first();
                $hasChoices = $product->activeVariants->count() > 1;
            @endphp
            <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <a href="{{ route('product.detail', $product->slug) }}" wire:navigate>
                    @if($image)
                        <img src="{{ Storage::url($image->image_path) }}" alt="{{ $image->alt_text ?: $product->name }}" class="h-52 w-full object-cover">
                    @else
                        <div class="flex h-52 items-center justify-center bg-slate-100 text-slate-400">Belum ada gambar</div>
                    @endif
                </a>
                <div class="p-4">
                    <div class="text-xs font-medium text-indigo-600">{{ $product->category->name }}</div>
                    <a href="{{ route('product.detail', $product->slug) }}" class="mt-1 block font-bold text-slate-900" wire:navigate>{{ $product->name }}</a>
                    <div class="mt-2 text-lg font-extrabold text-indigo-600">Rp {{ number_format($variant?->price ?? 0, 0, ',', '.') }}</div>
                    <div class="mt-1 text-xs text-slate-500">{{ $hasChoices ? $product->activeVariants->count().' pilihan varian' : 'Stok '.($variant?->stock_quantity ?? 0) }}</div>
                    @if($hasChoices)
                        <a href="{{ route('product.detail', $product->slug) }}" class="mt-4 block rounded-lg bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white" wire:navigate>Pilih varian</a>
                    @else
                        <button wire:click="addToCart({{ $variant?->id }})" wire:loading.attr="disabled" @disabled(!$variant?->isPurchasable()) class="mt-4 w-full rounded-lg px-4 py-2 text-sm font-semibold text-white {{ $variant?->isPurchasable() ? 'bg-slate-900' : 'cursor-not-allowed bg-slate-400' }}">Tambah ke keranjang</button>
                    @endif
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-xl border-2 border-dashed border-slate-200 py-16 text-center text-slate-500">Produk tidak ditemukan.</div>
        @endforelse
    </div>

    <div class="mt-8">{{ $products->links() }}</div>
</div>
