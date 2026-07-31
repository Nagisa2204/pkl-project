<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Order;

#[Layout('layouts.admin')]
class AdminOrderHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orders = Order::with('user');

        if ($this->search) {
            $orders->where(function ($query) {
                $query->where('invoice_no', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($user) {
                        $user->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }
        return view('livewire.admin.admin-order-history', [
            'orders' => $orders->latest()->paginate(10)
        ]);
    }
}
