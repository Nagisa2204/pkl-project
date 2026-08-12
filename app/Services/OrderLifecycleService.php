<?php

namespace App\Services;

use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShipmentStatus;
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
            $pending = $transactionStatus === 'pending'
                || ($transactionStatus === 'capture' && $fraudStatus !== 'accept');

            /*
             * Status final tidak boleh diturunkan oleh webhook yang terlambat.
             * Order gagal/kedaluwarsa juga tidak diaktifkan kembali karena stok
             * sudah dilepas; transaksi tersebut perlu ditinjau secara manual.
             */
            if ($order->payment_status === PaymentStatus::Paid && ! $paid) {
                return $order->fresh();
            }

            if (in_array($order->payment_status, [PaymentStatus::Failed, PaymentStatus::Expired], true) && ! $failed) {
                return $order->fresh();
            }

            $rawResponse = $payload;
            unset($rawResponse['signature_key']);

            $payment = Payment::query()
                ->where('order_id', $order->id)
                ->where('provider', 'midtrans')
                ->lockForUpdate()
                ->first();

            $paymentType = (string) ($payload['payment_type'] ?? $payment?->payment_type ?? 'midtrans_snap');

            Payment::updateOrCreate(
                ['order_id' => $order->id, 'provider' => 'midtrans'],
                [
                    'provider_order_id' => $orderNo,
                    'transaction_id' => $payload['transaction_id'] ?? null,
                    'payment_type' => $paymentType,
                    'bank' => $payload['bank'] ?? $payload['va_numbers'][0]['bank'] ?? $payment?->bank,
                    'va_number' => $payload['va_numbers'][0]['va_number'] ?? $payload['permata_va_number'] ?? null,
                    'bill_key' => $payload['bill_key'] ?? null,
                    'biller_code' => $payload['biller_code'] ?? null,
                    'status' => $transactionStatus,
                    'fraud_status' => $payload['fraud_status'] ?? null,
                    'status_code' => $payload['status_code'] ?? null,
                    'gross_amount' => $grossAmount,
                    'raw_response' => $rawResponse,
                    'paid_at' => $paid ? ($order->paid_at ?? now()) : $payment?->paid_at,
                ]
            );

            if ($paid && $order->payment_status !== PaymentStatus::Paid) {
                $order->update([
                    'payment_status' => PaymentStatus::Paid,
                    'payment_method' => $paymentType,
                    'status' => OrderStatus::Processing,
                    'fulfillment_status' => FulfillmentStatus::Processing,
                    'paid_at' => now(),
                ]);
                $event = 'paid';
            } elseif ($failed && $order->payment_status !== PaymentStatus::Paid) {
                $this->releaseStock($order);
                $order->shipments()->update(['status' => ShipmentStatus::Cancelled]);
                $order->update([
                    'payment_status' => $transactionStatus === 'expire' ? PaymentStatus::Expired : PaymentStatus::Failed,
                    'payment_method' => $paymentType,
                    'status' => OrderStatus::Cancelled,
                    'delivery_status' => ShipmentStatus::Cancelled,
                    'fulfillment_status' => FulfillmentStatus::Cancelled,
                    'cancelled_at' => $order->cancelled_at ?? now(),
                ]);
                $event = $transactionStatus === 'expire' ? 'expired' : 'failed';
            } elseif ($pending && $order->payment_status !== PaymentStatus::Paid) {
                $order->update([
                    'payment_status' => PaymentStatus::Pending,
                    'payment_method' => $paymentType,
                    'status' => OrderStatus::PendingPayment,
                ]);
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

    public function updateShipment(Order $order, ShipmentStatus|string $status, ?string $awbNumber = null): Order
    {
        $nextStatus = is_string($status) ? ShipmentStatus::tryFrom($status) : $status;

        if (! $nextStatus) {
            throw ValidationException::withMessages(['shipment_status' => 'Status pengiriman tidak valid.']);
        }

        $event = null;
        $order = DB::transaction(function () use ($order, $nextStatus, $awbNumber, &$event) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $shipment = $lockedOrder->shipments()->lockForUpdate()->firstOrFail();

            $currentStatus = $shipment->status;

            if (! $currentStatus->canTransitionTo($nextStatus)) {
                throw ValidationException::withMessages([
                    'shipment_status' => "Status {$currentStatus->label()} tidak dapat diubah menjadi {$nextStatus->label()}.",
                ]);
            }

            if ($nextStatus === ShipmentStatus::Shipped && blank($awbNumber ?? $shipment->awb_number)) {
                throw ValidationException::withMessages(['awb_number' => 'Nomor resi wajib diisi saat pesanan dikirim.']);
            }

            $shipment->update([
                'status' => $nextStatus,
                'awb_number' => $awbNumber ?: $shipment->awb_number,
                'shipped_at' => $nextStatus === ShipmentStatus::Shipped ? ($shipment->shipped_at ?? now()) : $shipment->shipped_at,
                'delivered_at' => $nextStatus === ShipmentStatus::Delivered ? ($shipment->delivered_at ?? now()) : $shipment->delivered_at,
            ]);

            if ($nextStatus === ShipmentStatus::Packing) {
                $lockedOrder->update([
                    'status' => OrderStatus::Processing,
                    'delivery_status' => ShipmentStatus::Packing,
                    'fulfillment_status' => FulfillmentStatus::Processing,
                ]);
                $event = 'processing';
            } elseif ($nextStatus === ShipmentStatus::Shipped) {
                $lockedOrder->update([
                    'status' => OrderStatus::Shipped,
                    'delivery_status' => ShipmentStatus::Shipped,
                    'fulfillment_status' => FulfillmentStatus::Fulfilled,
                ]);
                $event = 'shipped';
            } elseif ($nextStatus === ShipmentStatus::Delivered) {
                $lockedOrder->update([
                    'status' => OrderStatus::Completed,
                    'delivery_status' => ShipmentStatus::Delivered,
                    'fulfillment_status' => FulfillmentStatus::Fulfilled,
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
