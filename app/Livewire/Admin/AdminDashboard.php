<?php

namespace App\Livewire\Admin;

use App\Enums\PaymentStatus;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.admin')]
class AdminDashboard extends Component
{
    public float $totalRevenue = 0;
    public int $totalTransactions = 0;
    public int $totalCustomers = 0;
    public int $totalItemsSold = 0;

    public array $chartLabels = [];
    public array $chartValues = [];

    public string $period = '7days';

    public function mount()
    {
        $this->authorize('admin');
        $this->loadStatistics();
        $this->loadChart();
    }

    public function updatedPeriod()
    {
        $this->loadChart();
        $this->dispatch('chart-updated');
    }

    private function loadStatistics()
    {
        $this->totalRevenue = (float) Order::query()
            ->where(function ($query) {
                $query->whereNotNull('paid_at')
                    ->orWhere('payment_status', PaymentStatus::Paid->value);
            })
            ->sum('total');

        $this->totalTransactions = Order::query()
            ->where(function ($query) {
                $query->whereNotNull('paid_at')
                    ->orWhere('payment_status', PaymentStatus::Paid->value);
            })
            ->count();

        $this->totalCustomers = User::query()
            ->where('role', 'user')
            ->count();

        $this->totalItemsSold = (int) DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where(function ($query) {
                $query->whereNotNull('orders.paid_at')
                    ->orWhere('orders.payment_status', PaymentStatus::Paid->value);
            })
            ->whereNull('order_items.deleted_at')
            ->sum('order_items.quantity');
    }

    private function loadChart()
    {
        $this->chartLabels = [];
        $this->chartValues = [];

        switch ($this->period) {

            case '7days':

                $startDate = now()->subDays(6)->startOfDay();

                $salesData = Order::query()
                    ->selectRaw('DATE(created_at) as date_group, SUM(total) as total_sales')
                    ->where('created_at', '>=', $startDate)
                    ->where(function ($query) {
                        $query->whereNotNull('paid_at')
                            ->orWhere('payment_status', PaymentStatus::Paid->value);
                    })
                    ->groupBy('date_group')
                    ->get();

                for ($i = 6; $i >= 0; $i--) {

                    $date = now()->subDays($i);

                    $record = $salesData->first(
                        fn($item) => $item->date_group === $date->format('Y-m-d')
                    );

                    $this->chartLabels[] = $date->format('d M');

                    $this->chartValues[] = $record
                        ? (float) $record->total_sales
                        : 0;
                }

                break;

            case '1month':

                $startDate = now()->subDays(29)->startOfDay();

                $salesData = Order::query()
                    ->selectRaw('DATE(created_at) as date_group, SUM(total) as total_sales')
                    ->where('created_at', '>=', $startDate)
                    ->where(function ($query) {
                        $query->whereNotNull('paid_at')
                            ->orWhere('payment_status', PaymentStatus::Paid->value);
                    })
                    ->groupBy('date_group')
                    ->get();

                for ($i = 29; $i >= 0; $i--) {

                    $date = now()->subDays($i);

                    $record = $salesData->first(
                        fn($item) => $item->date_group === $date->format('Y-m-d')
                    );

                    $this->chartLabels[] = $date->format('d M');

                    $this->chartValues[] = $record
                        ? (float) $record->total_sales
                        : 0;
                }

                break;

            case '1year':

                $salesData = Order::query()
                    ->selectRaw("
                        YEAR(created_at) as year,
                        MONTH(created_at) as month,
                        SUM(total) as total_sales
                    ")
                    ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
                    ->where(function ($query) {
                        $query->whereNotNull('paid_at')
                            ->orWhere('payment_status', PaymentStatus::Paid->value);
                    })
                    ->groupBy('year', 'month')
                    ->get();

                for ($i = 11; $i >= 0; $i--) {

                    $month = now()->subMonths($i);

                    $record = $salesData->first(function ($item) use ($month) {
                        return $item->year == $month->year
                            && $item->month == $month->month;
                    });

                    $this->chartLabels[] = $month->translatedFormat('M');

                    $this->chartValues[] = $record
                        ? (float) $record->total_sales
                        : 0;
                }

                break;
        }
    }

    public function render()
    {
        $recentOrders = Order::query()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.admin.admin-dashboard', [
            'recentOrders' => $recentOrders,
        ]);
    }
}
