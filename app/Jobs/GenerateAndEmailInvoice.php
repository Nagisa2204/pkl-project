<?php

namespace App\Jobs;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\StoreSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GenerateAndEmailInvoice implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    public int $uniqueFor = 600;

    public function __construct(public readonly int $orderId) {}

    public function uniqueId(): string
    {
        return (string) $this->orderId;
    }

    public function handle(StoreSettingsService $settings): void
    {
        $order = Order::with(['items', 'payments', 'shipments', 'invoice'])->findOrFail($this->orderId);
        $store = $settings->get();

        if (! $order->invoice->generated_at || ! $order->invoice->file_path) {
            $path = 'invoices/'.$order->invoice_no.'.html';
            Storage::disk('local')->put($path, view('invoices.show', compact('order', 'store'))->render());
            $order->invoice->update(['file_path' => $path, 'generated_at' => now()]);
            $order->load('invoice');
        }

        if (! $order->invoice->emailed_at) {
            Mail::to($order->buyer_email)->send(new InvoiceMail($order, $store, 'customer'));
            $order->invoice->update(['emailed_at' => now(), 'email_sending_at' => null]);
        }

        if ($store->email && ! $order->invoice->admin_emailed_at) {
            Mail::to($store->email)->send(new InvoiceMail($order, $store, 'admin'));
            $order->invoice->update(['admin_emailed_at' => now(), 'admin_email_sending_at' => null]);
        }
    }
}
