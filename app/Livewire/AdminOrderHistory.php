<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

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
        return view('livewire.admin-order-history', [
            'orders' => $orders->latest()->paginate(10)
        ]);
    }
}
