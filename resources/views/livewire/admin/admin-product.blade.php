<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-content">Manajemen Produk</h1>
            <p class="mt-1 text-sm text-muted">Kelola katalog, varian, harga, dan persediaan.</p>
        </div>
        <x-ui.button :href="route('admin.products.create')" variant="success">Tambah produk</x-ui.button>
    </div>

    <x-ui.card>
        <div class="grid gap-4 md:grid-cols-[minmax(240px,1fr)_280px]">
            <div>
                <label class="ui-field-label" for="admin-product-search">Cari produk</label>
                <input id="admin-product-search" wire:model.live.debounce.300ms="search" type="search" class="ui-field"
                    placeholder="Nama produk">
            </div>
            <x-ui.searchable-select wire:model.live="selectedCategory" :options="$categories" label="Kategori"
                placeholder="Semua kategori" search-placeholder="Cari kategori..." />
        </div>
    </x-ui.card>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>SKU default</th>
                    <th>Kategori</th>
                    <th>Harga mulai</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr wire:key="admin-product-{{ $product->id }}">
                        <td class="font-semibold">{{ $product->name }}</td>
                        <td>{{ $product->defaultVariant?->sku ?? '-' }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>Rp {{ number_format($product->activeVariants->min('price') ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $product->activeVariants->sum('stock_quantity') }}</td>
                        <td><x-ui.badge :variant="$product->is_active ? 'success' : 'danger'">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
                        </td>
                        <td>
                            <div class="ui-table-actions"><x-ui.button :href="route('admin.products.manage', $product->id)" variant="secondary"
                                    size="sm">Kelola</x-ui.button></div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-muted">Produk tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $products->links() }}</div>
</div>
