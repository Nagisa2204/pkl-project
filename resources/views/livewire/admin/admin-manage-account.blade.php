<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-content">Kelola Akun Pengguna</h1>
        <p class="mt-1 text-sm text-muted">Kelola akses pelanggan dan administrator toko.</p>
    </div>

    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..." class="ui-field w-full sm:w-80">
        <x-ui.button wire:click="create" variant="success">Tambah Pengguna</x-ui.button>
    </div>

    @if($isModalOpen)
        <div class="ui-modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="account-modal-title">
            <div class="ui-modal-panel w-full max-w-lg">
                <div class="ui-card-header">
                    <h2 id="account-modal-title" class="text-lg font-bold text-content">{{ $isEditMode ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h2>
                </div>
                <form wire:submit="store" class="ui-card-body space-y-4">
                    <div>
                        <label class="ui-field-label" for="account-name">Nama lengkap</label>
                        <input id="account-name" type="text" wire:model="name" class="ui-field">
                        <x-input-error :messages="$errors->get('name')" />
                    </div>
                    <div>
                        <label class="ui-field-label" for="account-email">Email</label>
                        <input id="account-email" type="email" wire:model="email" class="ui-field">
                        <x-input-error :messages="$errors->get('email')" />
                    </div>
                    <div>
                        <label class="ui-field-label" for="account-password">Password</label>
                        <input id="account-password" type="password" wire:model="password" class="ui-field">
                        @if($isEditMode)<p class="mt-1 text-xs text-muted">Kosongkan jika password tidak diubah.</p>@endif
                        <x-input-error :messages="$errors->get('password')" />
                    </div>
                    <div>
                        <label class="ui-field-label" for="account-role">Peran</label>
                        <select id="account-role" wire:model="role" class="ui-field">
                            <option value="user">Pelanggan</option>
                            <option value="admin">Administrator</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" />
                    </div>
                    <label class="flex items-center gap-2 text-sm font-medium text-content">
                        <input type="checkbox" wire:model="is_active" class="rounded">
                        Akun aktif dan dapat login
                    </label>
                    <x-input-error :messages="$errors->get('is_active')" />

                    <div class="ui-form-actions">
                        <x-ui.button type="button" wire:click="closeModal" variant="outline">Batal</x-ui.button>
                        <x-ui.button type="submit" wire:loading.attr="disabled" wire:target="store">
                            <span wire:loading.remove wire:target="store">Simpan</span>
                            <span wire:loading wire:target="store">Menyimpan...</span>
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="ui-table-wrap">
        <table class="ui-table min-w-[860px]">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Status</th>
                    <th>Bergabung</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr wire:key="account-{{ $user->id }}">
                        <td class="font-semibold text-content">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role === 'admin' ? 'Administrator' : 'Pelanggan' }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <x-ui.badge :variant="$user->is_active ? 'success' : 'danger'">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
                                <x-ui.button wire:click="toggleActive({{ $user->id }})" variant="ghost" size="sm">{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</x-ui.button>
                            </div>
                        </td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="ui-table-actions">
                                <x-ui.button wire:click="edit({{ $user->id }})" variant="secondary" size="sm">Edit</x-ui.button>
                                <x-ui.confirm-action action="deleteUser({{ $user->id }})" title="Hapus pengguna" message="Akun {{ $user->name }} akan dihapus jika tidak memiliki transaksi." confirm-label="Hapus" button-variant="danger" size="sm">Hapus</x-ui.confirm-action>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-10 text-center text-muted">Tidak ada data pengguna.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $users->links() }}</div>
</div>
