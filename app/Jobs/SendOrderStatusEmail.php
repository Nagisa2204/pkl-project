<?php

namespace App\Jobs;

use App\Mail\OrderStatusMail;
use App\Models\Order;
use App\Services\StoreSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $orderId, public readonly string $event) {}

    public function handle(StoreSettingsService $settings): void
    {
        $order = Order::with(['items', 'payments', 'shipments'])->findOrFail($this->orderId);
        Mail::to($order->buyer_email)->send(new OrderStatusMail($order, $settings->get(), $this->event));
    }
}
