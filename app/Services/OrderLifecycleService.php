<?php

namespace App\Services;

use App\Jobs\GenerateAndEmailInvoice;
use App\Jobs\SendOrderStatusEmail;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderLifecycleService
{
    public function applyMidtransNotification(array $payload): Order
    {
        $orderNo = (string) ($payload['order_id'] ?? '');
        $grossAmount = (int) round((float) ($payload['gross_amount'] ?? -1));
        $event = null;

        $order = DB::transaction(function () use ($payload, $orderNo, $grossAmount, &$event) {
            $order = Order::query()->where('order_no', $orderNo)->lockForUpdate()->firstOrFail();

            if ($grossAmount !== $order->total) {
                throw ValidationException::withMessages(['gross_amount' => 'Nilai pembayaran tidak sesuai dengan total order.']);
            }

            $transactionStatus = strtolower((string) ($payload['transaction_status'] ?? ''));
            $fraudStatus = strtolower((string) ($payload['fraud_status'] ?? 'accept'));
            $paid = in_array($transactionStatus, ['settlement'], true)
                || ($transactionStatus === 'capture' && $fraudStatus === 'accept');
            $failed = in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'], true);

            $rawResponse = $payload;
            unset($rawResponse['signature_key']);

            Payment::updateOrCreate(
                ['order_id' => $order->id, 'provider' => 'midtrans'],
                [
                    'provider_order_id' => $orderNo,
                    'transaction_id' => $payload['transaction_id'] ?? null,
                    'payment_type' => $payload['payment_type'] ?? $order->payment_method,
                    'bank' => $payload['bank'] ?? $payload['va_numbers'][0]['bank'] ?? null,
                    'va_number' => $payload['va_numbers'][0]['va_number'] ?? $payload['permata_va_number'] ?? null,
                    'bill_key' => $payload['bill_key'] ?? null,
                    'biller_code' => $payload['biller_code'] ?? null,
                    'status' => $transactionStatus,
                    'fraud_status' => $payload['fraud_status'] ?? null,
                    'status_code' => $payload['status_code'] ?? null,
                    'gross_amount' => $grossAmount,
                    'raw_response' => $rawResponse,
                    'paid_at' => $paid ? ($order->paid_at ?? now()) : null,
                ]
            );

            if ($paid && $order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'fulfillment_status' => 'processing',
                    'paid_at' => now(),
                ]);
                $event = 'paid';
            } elseif ($failed && $order->payment_status !== 'paid') {
                $this->releaseStock($order);
                $order->update([
                    'payment_status' => $transactionStatus === 'expire' ? 'expired' : 'failed',
                    'status' => 'cancelled',
                    'fulfillment_status' => 'cancelled',
                    'cancelled_at' => $order->cancelled_at ?? now(),
                ]);
                $event = $transactionStatus === 'expire' ? 'expired' : 'failed';
            } elseif ($transactionStatus === 'pending' && $order->payment_status !== 'paid') {
                $order->update(['payment_status' => 'pending', 'status' => 'pending_payment']);
            }

            return $order->fresh();
        }, attempts: 3);

        if ($event === 'paid') {
            GenerateAndEmailInvoice::dispatch($order->id)->afterCommit();
            SendOrderStatusEmail::dispatch($order->id, 'paid')->afterCommit();
        } elseif ($event) {
            SendOrderStatusEmail::dispatch($order->id, $event)->afterCommit();
        }

        return $order;
    }

    public function updateShipment(Order $order, string $status, ?string $awbNumber = null): Order
    {
        if (! in_array($status, ['pending', 'processing', 'shipped', 'delivered'], true)) {
            throw ValidationException::withMessages(['shipment_status' => 'Status pengiriman tidak valid.']);
        }

        $event = null;
        $order = DB::transaction(function () use ($order, $status, $awbNumber, &$event) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $shipment = $lockedOrder->shipments()->lockForUpdate()->firstOrFail();

            if ($status === 'shipped' && blank($awbNumber ?? $shipment->awb_number)) {
                throw ValidationException::withMessages(['awb_number' => 'Nomor resi wajib diisi saat pesanan dikirim.']);
            }

            $shipment->update([
                'status' => $status,
                'awb_number' => $awbNumber ?: $shipment->awb_number,
                'shipped_at' => $status === 'shipped' ? ($shipment->shipped_at ?? now()) : $shipment->shipped_at,
                'delivered_at' => $status === 'delivered' ? ($shipment->delivered_at ?? now()) : $shipment->delivered_at,
            ]);

            if ($status === 'processing') {
                $lockedOrder->update(['status' => 'processing', 'fulfillment_status' => 'processing']);
                $event = 'processing';
            } elseif ($status === 'shipped') {
                $lockedOrder->update(['status' => 'shipped', 'delivery_status' => 'shipped', 'fulfillment_status' => 'fulfilled']);
                $event = 'shipped';
            } elseif ($status === 'delivered') {
                $lockedOrder->update([
                    'status' => 'completed',
                    'delivery_status' => 'delivered',
                    'fulfillment_status' => 'fulfilled',
                    'completed_at' => now(),
                ]);
                $event = 'completed';
            }

            return $lockedOrder->fresh();
        });

        if ($event) {
            SendOrderStatusEmail::dispatch($order->id, $event)->afterCommit();
        }

        return $order;
    }

    private function releaseStock(Order $order): void
    {
        if ($order->stock_released_at) {
            return;
        }

        $order->loadMissing('items.variant');

        foreach ($order->items as $item) {
            if ($item->stock_reserved && $item->variant) {
                $item->variant()->lockForUpdate()->first()->increment('stock_quantity', $item->quantity);
            }
        }

        $order->forceFill(['stock_released_at' => now()])->save();
    }
}
