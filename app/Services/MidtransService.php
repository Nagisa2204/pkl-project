<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use RuntimeException;

class MidtransService
{
    public function createSnapToken(Order $order): string
    {
        $this->configure();

        return Snap::getSnapToken($this->buildSnapPayload($order));
    }

    public function buildSnapPayload(Order $order): array
    {
        $order->loadMissing('items');

        return [
            'transaction_details' => [
                'order_id' => $order->order_no,
                'gross_amount' => $order->total,
            ],
            'item_details' => $order->items->map(fn ($item) => [
                'id' => $item->sku,
                'price' => $item->product_price,
                'quantity' => $item->quantity,
                'name' => mb_substr($item->product_name.' '.$item->variant_name, 0, 50),
            ])->push([
                'id' => 'SHIPPING',
                'price' => $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Biaya Pengiriman',
            ])->when($order->payment_fee > 0, fn ($items) => $items->push([
                'id' => 'PAYMENT-FEE',
                'price' => $order->payment_fee,
                'quantity' => 1,
                'name' => 'Biaya Pembayaran',
            ]))->values()->all(),
            'customer_details' => [
                'first_name' => $order->buyer_name,
                'email' => $order->buyer_email,
                'phone' => $order->buyer_whatsapp,
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'minutes',
                'duration' => config('midtrans.expiry_minutes', 60),
            ],
        ];
    }

    public function hasValidSignature(array $payload): bool
    {
        $serverKey = (string) config('midtrans.server_key');

        if ($serverKey === '' || empty($payload['signature_key'])) {
            return false;
        }

        $expected = hash('sha512',
            ($payload['order_id'] ?? '').
            ($payload['status_code'] ?? '').
            ($payload['gross_amount'] ?? '').
            $serverKey
        );

        return hash_equals($expected, (string) $payload['signature_key']);
    }

    private function configure(): void
    {
        if (! config('midtrans.server_key')) {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum dikonfigurasi.');
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }
}
