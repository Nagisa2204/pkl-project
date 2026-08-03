<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

#[Layout('layouts.admin')]
class AdminManageAccount extends Component
{
    use WithPagination;

    public $search = '';
    
    public $userId;
    public $name;
    public $email;
    public $password;
    public $role = 'user';
    public $is_active = true;
    
    public $isModalOpen = false;
    public $isEditMode = false;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $this->resetForm();
        
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->is_active = (bool) $user->is_active;
        
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->userId),
            ],
            'role' => 'required|string',
            'is_active' => 'boolean',
        ];

        if (!$this->isEditMode) {
            $rules['password'] = 'required|min:8';
        } else {
            $rules['password'] = 'nullable|min:8';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(
            ['id' => $this->userId],
            $data
        );

        session()->flash('success', $this->isEditMode ? 'Akun berhasil diperbarui.' : 'Akun berhasil ditambahkan.');
        
        $this->closeModal();
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "Akun {$user->name} berhasil {$status}.");
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        session()->flash('success', 'Akun berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'user';
        $this->is_active = true;
        $this->isEditMode = false;
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.admin-manage-account', [
            'users' => $users
        ]);
    }
}