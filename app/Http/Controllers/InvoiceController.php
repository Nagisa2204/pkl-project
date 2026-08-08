<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\StoreSettingsService;
use Illuminate\Contracts\View\View;

class InvoiceController extends Controller
{
    public function show(Order $order, StoreSettingsService $settings): View
    {
        abort_unless(auth()->id() === $order->user_id || auth()->user()?->isAdmin(), 403);

        $order->load(['items', 'payments', 'shipments', 'invoice']);
        $store = $settings->get();

        return view('invoices.show', compact('order', 'store'));
    }
}
