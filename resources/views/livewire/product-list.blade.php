<div class="ui-page">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-content">Katalog Produk</h1>
        <p class="mt-1 text-sm text-muted">Cari dan pilih produk sesuai kebutuhan Anda.</p>
    </div>

    <x-ui.card class="mb-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[minmax(240px,1fr)_220px_150px_150px_190px] xl:items-end">
            <div>
                <label class="ui-field-label" for="product-search">Cari produk</label>
                <input id="product-search" wire:model.live.debounce.350ms="search" type="search" placeholder="Nama produk atau SKU" class="ui-field">
            </div>

            <x-ui.searchable-select
                wire:model.live="selectedCategory"
                :options="$categories"
                label="Kategori"
                placeholder="Semua kategori"
                search-placeholder="Cari kategori..."
            />

            <div>
                <label class="ui-field-label" for="min-price">Harga minimum</label>
                <input id="min-price" wire:model.live.debounce.500ms="minPrice" type="number" min="0" placeholder="Rp 0" class="ui-field">
            </div>

            <div>
                <label class="ui-field-label" for="max-price">Harga maksimum</label>
                <input id="max-price" wire:model.live.debounce.500ms="maxPrice" type="number" min="0" placeholder="Tanpa batas" class="ui-field">
            </div>

            <x-ui.searchable-select
                wire:model.live="sort"
                :options="[
                    'latest' => 'Terbaru',
                    'price_low' => 'Harga terendah',
                    'price_high' => 'Harga tertinggi',
                    'name' => 'Nama A–Z',
                ]"
                label="Urutkan"
                :clearable="false"
                placeholder="Urutkan produk"
            />
        </div>
    </x-ui.card>

    <div wire:loading.flex class="mb-4 items-center gap-2 text-sm text-primary">
        <span class="h-4 w-4 animate-spin rounded-full border-2 border-primary border-r-transparent"></span>
        Memuat produk...
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" wire:loading.class="opacity-60">
        @forelse($products as $product)
            <x-product-card :product="$product" wire:key="product-card-{{ $product->id }}" />
        @empty
            <div class="ui-empty-state col-span-full">
                <p class="font-semibold text-content">Produk tidak ditemukan</p>
                <p class="mt-1 text-sm">Coba ubah kata pencarian atau filter harga dan kategori.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">{{ $products->links() }}</div>
</div>
