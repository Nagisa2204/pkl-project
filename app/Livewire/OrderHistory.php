<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderHistory extends Component
{
    use WithPagination;

    public $search;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orderQuery = Order::where('user_id', Auth::id())->latest();

        if(!empty($this->search)) {
            $orderQuery->where('invoice_no', 'like', '%' . $this->search . '%');
        }

        return view('livewire.order-history', [
            'orders' => $orderQuery->paginate(10)
        ]);
    }
}
