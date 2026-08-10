<?php

namespace App\Services;

use App\Jobs\SendOrderStatusEmail;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateOrderService
{
    public function __construct(private readonly MidtransService $midtrans) {}

    public function create(
        User $user,
        array $cartItemIds,
        int $shippingAddressId,
        array $shippingRate,
        string $courier,
        string $paymentMethod,
        string $buyerName,
        string $buyerWhatsapp
    ): Order {
        $order = DB::transaction(function () use (
            $user, $cartItemIds, $shippingAddressId, $shippingRate, $courier,
            $paymentMethod, $buyerName, $buyerWhatsapp
        ) {
            $address = UserAddress::query()
                ->whereBelongsTo($user)
                ->lockForUpdate()
                ->findOrFail($shippingAddressId);

            $cart = Cart::query()->whereBelongsTo($user)->lockForUpdate()->firstOrFail();
            $ids = collect($cartItemIds)->map(fn ($id) => (int) $id)->unique()->values();

            $items = CartItem::query()
                ->where('cart_id', $cart->id)
                ->whereIn('id', $ids)
                ->lockForUpdate()
                ->get();

            if ($items->count() !== $ids->count() || $items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Pilihan keranjang tidak valid atau sudah berubah.',
                ]);
            }

            $items->load('variant.product', 'variant.optionValues.option');
            $subtotal = 0;
            $snapshots = [];

            foreach ($items as $item) {
                $variant = $item->variant;
                $variant->refresh();
                $variant->loadMissing('product', 'optionValues.option');

                if (! $variant->isPurchasable($item->quantity)) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$variant->product->name} ({$variant->displayName()}) tidak mencukupi.",
                    ]);
                }

                if ($variant->stock_status !== 'preorder') {
                    $updated = $variant->newQuery()
                        ->whereKey($variant->id)
                        ->where('stock_quantity', '>=', $item->quantity)
                        ->decrement('stock_quantity', $item->quantity);

                    if ($updated !== 1) {
                        throw ValidationException::withMessages(['cart' => 'Stok berubah. Silakan periksa keranjang kembali.']);
                    }
                }

                $lineSubtotal = $variant->price * $item->quantity;
                $subtotal += $lineSubtotal;
                $options = $variant->optionValues->mapWithKeys(
                    fn ($value) => [$value->option->name => $value->value]
                )->all();

                $snapshots[] = [
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_name' => $variant->displayName(),
                    'variant_options' => $options ?: null,
                    'sku' => $variant->sku,
                    'product_price' => $variant->price,
                    'quantity' => $item->quantity,
                    'weight_grams' => $variant->weight_grams,
                    'stock_reserved' => $variant->stock_status !== 'preorder',
                    'subtotal' => $lineSubtotal,
                ];
            }

            $shippingCost = (int) ($shippingRate['cost'] ?? 0);
            $orderNo = $this->uniqueNumber('ORD');
            $invoiceNo = $this->uniqueNumber('INV');

            $order = Order::create([
                'user_id' => $user->id,
                'order_no' => $orderNo,
                'invoice_no' => $invoiceNo,
                'buyer_name' => $buyerName,
                'buyer_email' => $user->email,
                'buyer_whatsapp' => $buyerWhatsapp,
                'shipping_address_id' => $address->id,
                'subtotal' => $subtotal,
                'discount_total' => 0,
                'shipping_cost' => $shippingCost,
                'payment_fee' => 0,
                'shipping_courier_code' => $courier,
                'shipping_courier_name' => strtoupper($courier),
                'shipping_service_code' => (string) ($shippingRate['service'] ?? ''),
                'shipping_service_name' => (string) ($shippingRate['description'] ?? $shippingRate['service'] ?? ''),
                'shipping_etd' => (string) ($shippingRate['etd'] ?? ''),
                'total' => $subtotal + $shippingCost,
                'payment_method' => $paymentMethod,
                'status' => 'pending_payment',
                'payment_status' => 'pending',
                'delivery_status' => 'pending',
                'fulfillment_status' => 'unfulfilled',
                'stock_reserved_at' => now(),
            ]);

            $order->items()->createMany($snapshots);

            Shipment::create([
                'order_id' => $order->id,
                'user_address_id' => $address->id,
                'receiver_name' => $address->receiver_name,
                'phone' => $address->phone ?: $buyerWhatsapp,
                'address_line' => $address->address_line,
                'province_name' => $address->province_name,
                'city_name' => $address->city_name,
                'district_name' => $address->district_name,
                'subdistrict_name' => $address->subdistrict_name,
                'postal_code' => $address->postal_code,
                'courier_note' => $address->courier_note,
                'destination_id' => $address->destination_id,
                'destination_label' => $address->destination_label,
                'courier_code' => $courier,
                'courier_name' => strtoupper($courier),
                'service_code' => (string) ($shippingRate['service'] ?? ''),
                'service_name' => (string) ($shippingRate['description'] ?? $shippingRate['service'] ?? ''),
                'cost' => $shippingCost,
                'etd' => (string) ($shippingRate['etd'] ?? ''),
                'status' => 'pending',
                'raw_response' => $shippingRate,
            ]);

            Payment::create([
                'order_id' => $order->id,
                'provider' => 'midtrans',
                'provider_order_id' => $orderNo,
                'payment_type' => $paymentMethod,
                'bank' => $paymentMethod,
                'status' => 'pending',
                'gross_amount' => $order->total,
                'expiry_at' => now()->addMinutes(config('midtrans.expiry_minutes', 60)),
            ]);

            Invoice::create(['order_id' => $order->id, 'invoice_no' => $invoiceNo]);
            CartItem::query()->whereIn('id', $items->pluck('id'))->delete();

            return $order;
        }, attempts: 3);

        try {
            $token = $this->midtrans->createSnapToken($order->fresh('items'));
            $order->payments()->where('provider', 'midtrans')->update(['snap_token' => $token]);
        } catch (\Throwable $exception) {
            Log::error('Midtrans Snap initialization failed', [
                'order_no' => $order->order_no,
                'exception' => $exception->getMessage(),
            ]);

            $order->payments()->where('provider', 'midtrans')->update(['status' => 'initialization_failed']);
        }

        try {
            SendOrderStatusEmail::dispatch($order->id, 'created')->afterCommit();
        } catch (\Throwable $exception) {
            Log::error('Order created email could not be queued', [
                'order_no' => $order->order_no,
                'exception' => $exception->getMessage(),
            ]);
        }

        return $order->fresh(['items', 'payments', 'shipments', 'invoice']);
    }

    private function uniqueNumber(string $prefix): string
    {
        return $prefix.'-'.now()->format('Ymd').'-'.Str::upper((string) Str::ulid());
    }
}
