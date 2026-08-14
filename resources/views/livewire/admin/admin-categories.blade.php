<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-content">
            Manajemen Kategori
        </h1>
        <p class="mt-1 text-sm text-muted"> Kelola kategori produk.</p>
    </div>
    <div class="grid gap-6 grid-cols-1 lg:grid-cols-[360px_minmax(0,1fr)]">
        <x-ui.card class="h-fit">
            <h1 class="text-xl font-bold text-content">{{ $editingId ? 'Edit' : 'Tambah' }} Kategori</h1>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="ui-field-label" for="category-name">Nama</label>
                    <input id="category-name" wire:model.live="name" class="ui-field">
                </div>
                <div>
                    <label class="ui-field-label" for="category-slug">Slug</label>
                    <input id="category-slug" wire:model="slug" class="ui-field">
                </div>
                <x-ui.searchable-select wire:model="parent_id"
                    :options="$categories->where('id', '!=', $editingId)"
                    label="Kategori induk"
                    placeholder="Tanpa induk"
                    search-placeholder="Cari kategori..."
                />
                <div>
                    <label class="ui-field-label" for="category-description">Deskripsi</label>
                    <textarea id="category-description" wire:model="description" class="ui-field"></textarea>
                </div>
                <label class="flex items-center gap-2 text-sm font-medium text-content"><input type="checkbox" wire:model="is_active"> Aktif</label>
            </div>
            @if ($errors->any())
                <x-ui.alert variant="danger" class="mt-4">{{ $errors->first() }}</x-ui.alert>
            @endif
            <x-ui.button wire:click="save" wire:loading.attr="disabled" wire:target="save" class="mt-4">Simpan kategori</x-ui.button>
        </x-ui.card>

        <x-ui.card :padding="false">
            <div class="ui-card-header"><h2 class="text-xl font-bold text-content">Kategori Produk</h2></div>
            <div class="ui-table-wrap rounded-none border-0">
                <table class="ui-table">
                    <thead><tr><th>Nama</th><th>Induk</th><th>Produk</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr wire:key="category-row-{{ $category->id }}">
                                <td class="font-semibold">{{ $category->name }}</td>
                                <td>{{ $category->parent?->name ?? '-' }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td><x-ui.badge :variant="$category->is_active ? 'success' : 'secondary'">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>
                                <td>
                                    <div class="ui-table-actions"><x-ui.button wire:click="edit({{ $category->id }})"
                                            variant="secondary" size="sm">Edit</x-ui.button>
                                        @if (!$category->is_protected && !$category->products_count)
                                            <x-ui.confirm-action action="delete({{ $category->id }})" title="Hapus kategori"
                                                message="Kategori {{ $category->name }} akan dihapus." confirm-label="Hapus"
                                                button-variant="danger" size="sm">Hapus</x-ui.confirm-action>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</div>
