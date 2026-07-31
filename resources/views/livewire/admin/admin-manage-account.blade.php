<div>
    <h2>Kelola Akun Pengguna</h2>
    
    @if(session()->has('success'))
        <div style="color: green; margin-bottom: 15px; padding: 10px; background-color: #e6ffed; border: 1px solid #b7ebc6; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
        <input type="text" wire:model.live="search" placeholder="Cari nama atau email..." style="padding: 8px; width: 250px;">
        <button wire:click="create" style="padding: 8px 15px; cursor: pointer;">Tambah Pengguna</button>
    </div>

    <!-- AREA MODAL FORM (MELAYANG DI TENGAH) -->
    @if($isModalOpen)
        <!-- Overlay Gelap di Background -->
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;">
            
            <!-- Kotak Bubble Putih Form -->
            <div style="background-color: white; padding: 25px; border-radius: 8px; width: 450px; max-width: 90%; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                    {{ $isEditMode ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
                </h3>
                
                <form wire:submit.prevent="store">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Nama Lengkap:</label>
                        <input type="text" wire:model="name" style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;">
                        @error('name') <span style="color: red; font-size: 12px; display: block; margin-top: 4px;">{{ $message }}</span> @enderror
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Email:</label>
                        <input type="email" wire:model="email" style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;">
                        @error('email') <span style="color: red; font-size: 12px; display: block; margin-top: 4px;">{{ $message }}</span> @enderror
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Password:</label>
                        <input type="password" wire:model="password" style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;">
                        @if($isEditMode)
                            <small style="color: gray; display: block; margin-top: 5px;">Kosongkan jika tidak ingin mengubah password lama.</small>
                        @endif
                        @error('password') <span style="color: red; font-size: 12px; display: block; margin-top: 4px;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Input Role -->
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Role:</label>
                        <select wire:model="role" style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('role') <span style="color: red; font-size: 12px; display: block; margin-top: 4px;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Input Status Aktif -->
                    <div style="margin-bottom: 25px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" wire:model="is_active" style="width: 16px; height: 16px;">
                            <span>Akun Aktif (Bisa Login)</span>
                        </label>
                        @error('is_active') <span style="color: red; font-size: 12px; display: block; margin-top: 4px;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tombol Aksi -->
                    <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #eee; padding-top: 15px;">
                        <button type="button" wire:click="closeModal" style="padding: 8px 15px; cursor: pointer; background-color: #f1f1f1; border: 1px solid #ccc; border-radius: 4px;">Batal</button>
                        <button type="submit" style="padding: 8px 15px; cursor: pointer; background-color: #007bff; color: white; border: none; border-radius: 4px;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- TABEL DATA -->
    <table border="1" width="100%" style="border-collapse: collapse; text-align: left; background-color: white;">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th style="padding: 12px 8px; border-bottom: 2px solid #dee2e6;">Nama</th>
                <th style="padding: 12px 8px; border-bottom: 2px solid #dee2e6;">Email</th>
                <th style="padding: 12px 8px; border-bottom: 2px solid #dee2e6;">Role</th>
                <th style="padding: 12px 8px; border-bottom: 2px solid #dee2e6;">Status</th>
                <th style="padding: 12px 8px; border-bottom: 2px solid #dee2e6;">Bergabung</th>
                <th style="padding: 12px 8px; border-bottom: 2px solid #dee2e6;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px 8px;">{{ $user->name }}</td>
                    <td style="padding: 10px 8px;">{{ $user->email }}</td>
                    <td style="padding: 10px 8px;">{{ ucfirst($user->role) }}</td>
                    <td style="padding: 10px 8px;">
                        @if($user->is_active)
                            <span style="color: white; background: #28a745; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;">Aktif</span>
                        @else
                            <span style="color: white; background: #dc3545; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;">Nonaktif</span>
                        @endif
                        
                        <button style="margin-left: 8px; font-size: 11px; padding: 2px 6px; cursor: pointer;" wire:click="toggleActive({{ $user->id }})">
                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </td>
                    <td style="padding: 10px 8px;">{{ $user->created_at->format('d M Y') }}</td>
                    <td style="padding: 10px 8px;">
                        <button wire:click="edit({{ $user->id }})" style="padding: 4px 8px; cursor: pointer; margin-right: 5px;">Edit</button>
                        <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Yakin ingin menghapus pengguna ini?" style="padding: 4px 8px; cursor: pointer; color: white; background: #dc3545; border: none; border-radius: 3px;">
                            Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Tidak ada data pengguna.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 15px;">
        {{ $users->links() }}
    </div>
</div>