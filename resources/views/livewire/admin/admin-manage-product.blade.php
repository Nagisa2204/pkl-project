<div class="space-y-6">
    <div class="flex items-center justify-between"><div><h1 class="text-2xl font-bold">{{ $product ? 'Kelola Produk' : 'Tambah Produk' }}</h1><p class="text-sm text-slate-500">Opsi bersifat generic: warna, ukuran, kapasitas, material, rasa, atau atribut lain.</p></div><a href="{{ route('admin.products') }}" class="rounded-lg border px-4 py-2">Kembali</a></div>
    @if(session('success'))<div class="rounded-lg bg-emerald-100 p-4 text-emerald-800">{{ session('success') }}</div>@endif

    <section class="rounded-xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-bold">Informasi Produk</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <div><label class="text-sm font-medium">Nama</label><input wire:model.live="name" class="mt-1 w-full rounded-lg border-slate-300"><x-input-error :messages="$errors->get('name')" /></div>
            <div><label class="text-sm font-medium">Kategori</label><select wire:model="category_id" class="mt-1 w-full rounded-lg border-slate-300"><option value="">Pilih kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select><x-input-error :messages="$errors->get('category_id')" /></div>
            <div><label class="text-sm font-medium">Slug</label><input wire:model="slug" class="mt-1 w-full rounded-lg border-slate-300"><x-input-error :messages="$errors->get('slug')" /></div>
            <div><label class="text-sm font-medium">Minimum order</label><input type="number" min="1" wire:model="min_order_quantity" class="mt-1 w-full rounded-lg border-slate-300"></div>
        </div>
        <div class="mt-4"><label class="text-sm font-medium">Deskripsi</label><textarea wire:model="description" rows="5" class="mt-1 w-full rounded-lg border-slate-300"></textarea></div>
        <label class="mt-4 flex items-center gap-2"><input type="checkbox" wire:model="is_active" class="rounded"> Produk aktif</label>
    </section>

    <section class="rounded-xl bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between"><div><h2 class="text-lg font-bold">Opsi Produk</h2><p class="text-sm text-slate-500">Pisahkan nilai dengan koma. Kosongkan opsi untuk produk tanpa pilihan.</p></div><button wire:click="addOption" type="button" class="rounded-lg border px-3 py-2 text-sm">Tambah opsi</button></div>
        <div class="mt-4 space-y-3">
            @foreach($options as $index => $option)
                <div class="grid gap-3 md:grid-cols-[220px_1fr_auto]" wire:key="option-{{ $index }}">
                    <input wire:model="options.{{ $index }}.name" placeholder="Contoh: Warna" class="rounded-lg border-slate-300">
                    <input wire:model="options.{{ $index }}.values" placeholder="Contoh: Hitam, Putih, Merah" class="rounded-lg border-slate-300">
                    <button wire:click="removeOption({{ $index }})" type="button" class="text-sm text-red-600">Hapus</button>
                </div>
            @endforeach
        </div>
        <button wire:click="generateVariants" type="button" class="mt-4 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Buat / sinkronkan kombinasi varian</button>
    </section>

    <section class="overflow-hidden rounded-xl bg-white shadow-sm">
        <div class="p-6"><h2 class="text-lg font-bold">Varian & Persediaan</h2><p class="text-sm text-slate-500">SKU, harga, stok, berat, dan preorder dikelola per kombinasi.</p></div>
        <div class="overflow-x-auto">
            <table class="min-w-[1200px] w-full text-sm">
                <thead class="bg-slate-50"><tr><th class="p-3 text-left">Varian</th><th>SKU</th><th>Harga</th><th>Harga coret</th><th>Stok</th><th>Status</th><th>Berat (g)</th><th>Preorder</th><th>Aktif</th></tr></thead>
                <tbody>
                @foreach($variants as $index => $variant)
                    <tr class="border-t" wire:key="variant-{{ $index }}-{{ $variant['label'] }}">
                        <td class="p-3 font-semibold">{{ $variant['label'] }}</td>
                        <td class="p-2"><input wire:model="variants.{{ $index }}.sku" class="w-40 rounded border-slate-300"><x-input-error :messages="$errors->get('variants.'.$index.'.sku')" /></td>
                        <td class="p-2"><input type="number" wire:model="variants.{{ $index }}.price" class="w-32 rounded border-slate-300"></td>
                        <td class="p-2"><input type="number" wire:model="variants.{{ $index }}.compare_at_price" class="w-32 rounded border-slate-300"></td>
                        <td class="p-2"><input type="number" wire:model="variants.{{ $index }}.stock_quantity" class="w-24 rounded border-slate-300"></td>
                        <td class="p-2"><select wire:model="variants.{{ $index }}.stock_status" class="rounded border-slate-300"><option value="available">Tersedia</option><option value="preorder">Preorder</option><option value="out_of_stock">Habis</option></select></td>
                        <td class="p-2"><input type="number" wire:model="variants.{{ $index }}.weight_grams" class="w-24 rounded border-slate-300"></td>
                        <td class="p-2"><input type="number" wire:model="variants.{{ $index }}.preorder_days" class="w-20 rounded border-slate-300"></td>
                        <td class="p-2 text-center"><input type="checkbox" wire:model="variants.{{ $index }}.is_active" class="rounded"></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-xl bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold">Galeri Produk</h2>
        <input type="file" wire:model="gallery" multiple accept="image/jpeg,image/png,image/webp" class="mt-3 block w-full rounded-lg border p-3">
        <x-input-error :messages="$errors->get('gallery.*')" />
        @if($product?->images->count())
            <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach($product->images as $image)
                    <div class="rounded-lg border p-2"><img src="{{ Storage::url($image->image_path) }}" class="h-32 w-full rounded object-cover"><div class="mt-2 flex justify-between text-xs">@if($image->is_primary)<span class="font-bold text-emerald-600">Utama</span>@else<button wire:click="setPrimaryImage({{ $image->id }})" class="text-indigo-600">Jadikan utama</button>@endif<button wire:click="deleteImage({{ $image->id }})" wire:confirm="Hapus gambar?" class="text-red-600">Hapus</button></div></div>
                @endforeach
            </div>
        @endif
    </section>

    <div class="flex justify-end gap-3">@if($product)<button wire:click="delete" wire:confirm="Nonaktifkan produk?" class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white">Nonaktifkan</button>@endif<button wire:click="save" wire:loading.attr="disabled" class="rounded-lg bg-emerald-600 px-5 py-2 font-semibold text-white">Simpan Produk</button></div>
</div>
