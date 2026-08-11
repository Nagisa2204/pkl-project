<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-content">{{ $product ? 'Kelola Produk' : 'Tambah Produk' }}</h1>
            <p class="mt-1 text-sm text-muted">Opsi dapat digunakan untuk warna, ukuran, kapasitas, material, rasa, atau
                atribut lainnya.</p>
        </div>
        <x-ui.button :href="route('admin.products')" variant="outline">Kembali</x-ui.button>
    </div>

    <x-ui.card>
        <h2 class="text-lg font-bold text-content">Informasi Produk</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
                <label class="ui-field-label" for="product-name">Nama produk</label>
                <input id="product-name" wire:model.live="name" class="ui-field">
                <x-input-error :messages="$errors->get('name')" />
            </div>
            <x-ui.searchable-select wire:model="category_id" :options="$categories" label="Kategori"
                placeholder="Pilih kategori" search-placeholder="Cari kategori..." />
            <div>
                <label class="ui-field-label" for="product-slug">Slug</label>
                <input id="product-slug" wire:model="slug" class="ui-field">
                <x-input-error :messages="$errors->get('slug')" />
            </div>
            <div>
                <label class="ui-field-label" for="minimum-order">Jumlah pesanan minimum</label>
                <input id="minimum-order" type="number" min="1" wire:model="min_order_quantity"
                    class="ui-field">
                <x-input-error :messages="$errors->get('min_order_quantity')" />
            </div>
        </div>
        <div class="mt-4">
            <label class="ui-field-label" for="product-description">Deskripsi</label>
            <textarea id="product-description" wire:model="description" rows="5" class="ui-field"></textarea>
            <x-input-error :messages="$errors->get('description')" />
        </div>
        <label class="mt-4 flex items-center gap-2 text-sm font-medium text-content">
            <input type="checkbox" wire:model="is_active" class="rounded">
            Produk aktif
        </label>
    </x-ui.card>

    <x-ui.card>
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
            <div>
                <h2 class="text-lg font-bold text-content">Opsi Produk</h2>
                <p class="mt-1 text-sm text-muted">Pisahkan setiap nilai dengan koma. Kosongkan opsi untuk produk tanpa
                    pilihan.</p>
            </div>
            <x-ui.button wire:click="addOption" variant="outline" size="sm">Tambah Opsi</x-ui.button>
        </div>

        <div class="mt-4 space-y-3">
            @foreach ($options as $index => $option)
                <div class="grid gap-3 rounded-ui border border-default p-3 md:grid-cols-[220px_1fr_auto] md:items-end"
                    wire:key="option-{{ $option['id'] ?? 'new-' . $index }}">
                    <div>
                        <label class="ui-field-label" for="option-name-{{ $index }}">Nama opsi</label>
                        <input id="option-name-{{ $index }}" wire:model="options.{{ $index }}.name"
                            placeholder="Contoh: Warna" class="ui-field">
                    </div>
                    <div>
                        <label class="ui-field-label" for="option-values-{{ $index }}">Nilai opsi</label>
                        <input id="option-values-{{ $index }}" wire:model="options.{{ $index }}.values"
                            placeholder="Contoh: Hitam, Putih, Merah" class="ui-field">
                    </div>
                    <x-ui.confirm-action action="removeOption({{ $index }})" title="Hapus opsi"
                        message="Kombinasi yang memakai opsi ini akan dihapus dari form setelah sinkronisasi. Data belum berubah di database sebelum produk disimpan."
                        confirm-label="Hapus Opsi" button-variant="danger" size="sm">Hapus</x-ui.confirm-action>
                </div>
            @endforeach
        </div>

        <x-input-error :messages="$errors->get('options')" class="mt-3" />
        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
            <x-ui.button wire:click="generateVariants" wire:loading.attr="disabled" wire:target="generateVariants">
                Sinkronkan Kombinasi Varian
            </x-ui.button>
            <p class="text-xs leading-5 text-muted">Varian lama dipertahankan. Kombinasi baru dibuat nonaktif dengan
                stok 0 agar dapat ditinjau terlebih dahulu.</p>
        </div>
    </x-ui.card>

    <x-ui.card :padding="false">
        <div class="ui-card-header">
            <h2 class="text-lg font-bold text-content">Varian dan Persediaan</h2>
            <p class="mt-1 text-sm text-muted">SKU, harga, stok, berat, dan preorder dikelola per kombinasi.</p>
            <x-input-error :messages="$errors->get('variants')" class="mt-2" />
        </div>
        <div class="ui-table-wrap rounded-none border-0">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Varian</th>
                        <th>SKU</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Harga Coret</th>
                        <th class="text-right">Stok</th>
                        <th>Status Stok</th>
                        <th class="text-right">Berat (g)</th>
                        <th class="text-right">Preorder (hari)</th>
                        <th class="text-center">Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($variants as $index => $variant)
                        <tr wire:key="variant-{{ $variant['id'] ?? md5(json_encode($variant['selection'])) }}">
                            <td class="font-semibold">
                                {{ $variant['label'] }}
                                @if (empty($variant['id']))
                                    <x-ui.badge variant="info" class="ml-1">Baru</x-ui.badge>
                                @endif
                            </td>
                            <td>
                                <input wire:model="variants.{{ $index }}.sku" class="ui-field w-44"
                                    aria-label="SKU {{ $variant['label'] }}">
                                <x-input-error :messages="$errors->get('variants.' . $index . '.sku')" />
                            </td>
                            <td><input type="number" min="0" wire:model="variants.{{ $index }}.price"
                                    class="ui-field w-32 text-right" aria-label="Harga {{ $variant['label'] }}"></td>
                            <td><input type="number" min="0"
                                    wire:model="variants.{{ $index }}.compare_at_price"
                                    class="ui-field w-32 text-right" aria-label="Harga coret {{ $variant['label'] }}">
                            </td>
                            <td><input type="number" min="0"
                                    wire:model="variants.{{ $index }}.stock_quantity"
                                    class="ui-field w-24 text-right" aria-label="Stok {{ $variant['label'] }}"></td>
                            <td>
                                <select wire:model="variants.{{ $index }}.stock_status" class="ui-field w-36"
                                    aria-label="Status stok {{ $variant['label'] }}">
                                    @foreach (\App\Enums\StockStatus::options() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" min="0"
                                    wire:model="variants.{{ $index }}.weight_grams"
                                    class="ui-field w-24 text-right" aria-label="Berat {{ $variant['label'] }}"></td>
                            <td><input type="number" min="0"
                                    wire:model="variants.{{ $index }}.preorder_days"
                                    class="ui-field w-24 text-right"
                                    aria-label="Waktu preorder {{ $variant['label'] }}"></td>
                            <td class="text-center"><input type="checkbox"
                                    wire:model="variants.{{ $index }}.is_active" class="rounded"
                                    aria-label="Aktifkan {{ $variant['label'] }}"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <x-ui.card>
        <h2 class="text-lg font-bold text-content">Galeri Produk</h2>
        <input type="file" wire:model="gallery" multiple accept="image/jpeg,image/png,image/webp"
            class="ui-field mt-3 p-2">
        <x-input-error :messages="$errors->get('gallery.*')" />
        @if ($product?->images->count())
            <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach ($product->images as $image)
                    <div class="rounded-ui border border-default p-2">
                        <img src="{{ Storage::url($image->image_path) }}"
                            alt="{{ $image->alt_text ?: $product->name }}"
                            class="h-32 w-full rounded-ui object-cover">
                        <div class="mt-2 flex flex-wrap items-center justify-between gap-2 text-xs">
                            @if ($image->is_primary)
                                <x-ui.badge variant="success">Utama</x-ui.badge>
                            @else
                                <x-ui.button wire:click="setPrimaryImage({{ $image->id }})" variant="ghost"
                                    size="sm">Jadikan Utama</x-ui.button>
                            @endif
                            <x-ui.confirm-action action="deleteImage({{ $image->id }})" title="Hapus gambar"
                                message="Gambar produk ini akan dihapus permanen dari penyimpanan."
                                confirm-label="Hapus" button-variant="danger"
                                size="sm">Hapus</x-ui.confirm-action>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui.card>

    <div class="ui-form-actions">
        @if ($product)
            <x-ui.confirm-action action="delete()" title="Nonaktifkan produk"
                message="Produk tidak akan tampil di katalog, tetapi histori transaksi tetap dipertahankan."
                confirm-label="Nonaktifkan" button-variant="danger">Nonaktifkan</x-ui.confirm-action>
        @endif
        <x-ui.button wire:click="save" wire:loading.attr="disabled" wire:target="save" variant="success">
            <span wire:loading.remove wire:target="save">Simpan Produk</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </x-ui.button>
    </div>
</div>
