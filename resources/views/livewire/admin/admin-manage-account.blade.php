<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-800">
        Kelola Akun Pengguna
    </h1>

    @if(session()->has('success'))
        <div class="rounded-lg bg-green-100 p-4 text-green-700 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between gap-4">
        <input type="text" wire:model.live="search" placeholder="Cari nama atau email..." class="w-64 rounded-lg border border-gray-300 p-2 text-sm focus:border-blue-500 focus:outline-none">
        
        <button wire:click="create" class="rounded-xl border border-emerald-600 bg-emerald-300 px-4 py-2 text-sm font-semibold text-emerald-950 hover:bg-emerald-400 transition">
            Tambah Pengguna
        </button>
    </div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 border-b border-gray-200 pb-3 text-lg font-bold text-gray-800">
                    {{ $isEditMode ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
                </h3>
                
                <form wire:submit.prevent="store">
                    <table class="w-full border-separate border-spacing-y-3">
                        <tbody>
                            <tr>
                                <td class="w-1/3 align-top pt-2">
                                    <label class="block text-sm font-bold text-gray-700">
                                        Nama Lengkap
                                    </label>
                                </td>
                                <td class="w-2/3 align-top">
                                    <input type="text" wire:model="name" class="w-full rounded border border-gray-300 p-2 text-sm focus:border-blue-500 focus:outline-none">
                                    @error('name') 
                                        <span class="mt-1 block text-xs text-red-500">
                                            {{ $message }}
                                        </span> 
                                    @enderror
                                </td>
                            </tr>

                            <tr>
                                <td class="align-top pt-2">
                                    <label class="block text-sm font-bold text-gray-700">
                                        Email
                                    </label>
                                </td>
                                <td class="align-top">
                                    <input type="email" wire:model="email" class="w-full rounded border border-gray-300 p-2 text-sm focus:border-blue-500 focus:outline-none">
                                    @error('email') 
                                        <span class="mt-1 block text-xs text-red-500">
                                            {{ $message }}
                                        </span> 
                                    @enderror
                                </td>
                            </tr>

                            <tr>
                                <td class="align-top pt-2">
                                    <label class="block text-sm font-bold text-gray-700">
                                        Password
                                    </label>
                                </td>
                                <td class="align-top">
                                    <input type="password" wire:model="password" class="w-full rounded border border-gray-300 p-2 text-sm focus:border-blue-500 focus:outline-none">
                                    @if($isEditMode)
                                        <small class="mt-1 block text-xs text-gray-500">
                                            Kosongkan jika tidak ingin mengubah password lama.
                                        </small>
                                    @endif
                                    @error('password') 
                                        <span class="mt-1 block text-xs text-red-500">
                                            {{ $message }}
                                        </span> 
                                    @enderror
                                </td>
                            </tr>

                            <tr>
                                <td class="align-top pt-2">
                                    <label class="block text-sm font-bold text-gray-700">
                                        Role
                                    </label>
                                </td>
                                <td class="align-top">
                                    <select wire:model="role" class="w-full rounded border border-gray-300 p-2 text-sm focus:border-blue-500 focus:outline-none bg-white">
                                        <option value="user">
                                            User
                                        </option>
                                        <option value="admin">
                                            Admin
                                        </option>
                                    </select>
                                    @error('role') 
                                        <span class="mt-1 block text-xs text-red-500">
                                            {{ $message }}
                                        </span> 
                                    @enderror
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td class="align-top">
                                    <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-gray-700">
                                        <input type="checkbox" wire:model="is_active" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span>
                                            Akun Aktif (Bisa Login)
                                        </span>
                                    </label>
                                    @error('is_active') 
                                        <span class="mt-1 block text-xs text-red-500">
                                            {{ $message }}
                                        </span> 
                                    @enderror
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="flex justify-end gap-2 border-t border-gray-200 pt-4 mt-4">
                        <button type="button" wire:click="closeModal" class="rounded border border-gray-300 bg-gray-100 px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full border-collapse text-center bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-2 border-b-2 border-gray-200 font-semibold text-gray-700">
                        Nama
                    </th>
                    <th class="py-3 px-2 border-b-2 border-gray-200 font-semibold text-gray-700">
                        Email
                    </th>
                    <th class="py-3 px-2 border-b-2 border-gray-200 font-semibold text-gray-700">
                        Role
                    </th>
                    <th class="py-3 px-2 border-b-2 border-gray-200 font-semibold text-gray-700">
                        Status
                    </th>
                    <th class="py-3 px-2 border-b-2 border-gray-200 font-semibold text-gray-700">
                        Bergabung
                    </th>
                    <th class="py-3 px-2 border-b-2 border-gray-200 font-semibold text-gray-700">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-b border-gray-200 hover:bg-gray-50/50">
                        <td class="py-2.5 px-2 text-gray-800">
                            {{ $user->name }}
                        </td>
                        <td class="py-2.5 px-2 text-gray-800">
                            {{ $user->email }}
                        </td>
                        <td class="py-2.5 px-2 text-gray-800">
                            {{ ucfirst($user->role) }}
                        </td>
                        <td class="py-2.5 px-2">
                            @if($user->is_active)
                                <span class="rounded-full bg-green-600 px-2 py-0.5 text-xs font-bold text-white">
                                    Aktif
                                </span>
                            @else
                                <span class="rounded-full bg-red-600 px-2 py-0.5 text-xs font-bold text-white">
                                    Nonaktif
                                </span>
                            @endif
                        
                            <button class="ml-2 rounded-full bg-gray-200 px-2 py-0.5 text-xs font-bold text-gray-600 hover:bg-gray-100" wire:click="toggleActive({{ $user->id }})">
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </td>
                        <td class="py-2.5 px-2 text-gray-800">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="py-2.5 px-2 space-x-1">
                            <button wire:click="edit({{ $user->id }})" class="rounded-lg bg-indigo-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-indigo-700 transition">
                                Edit
                            </button>
                            <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Yakin ingin menghapus pengguna ini?" class="rounded-lg bg-red-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-red-700 transition">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-5 text-center text-gray-500">
                            Tidak ada data pengguna.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>