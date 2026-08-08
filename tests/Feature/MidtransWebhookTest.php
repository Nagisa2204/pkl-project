<?php

use App\Jobs\GenerateAndEmailInvoice;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Queue;

function webhookOrder(array $overrides = []): array
{
    $user = User::factory()->create();
    $address = UserAddress::create([
        'user_id' => $user->id, 'label' => 'Rumah', 'receiver_name' => $user->name,
        'phone' => '08123456789', 'address_line' => 'Jalan Test', 'province_name' => 'NTB',
        'city_name' => 'Mataram', 'district_name' => 'Ampenan', 'subdistrict_name' => 'Taman Sari',
        'postal_code' => '83112', 'destination_id' => 1, 'destination_label' => 'Taman Sari', 'is_default' => true,
    ]);
    $variant = Product::factory()->for(Category::factory())->create()->variants()->create([
        'sku' => 'WEBHOOK-SKU-'.uniqid(), 'combination_key' => 'default', 'price' => 100000,
        'stock_quantity' => 3, 'stock_status' => 'available', 'weight_grams' => 100,
        'is_default' => true, 'is_active' => true,
    ]);
    $order = Order::create(array_merge([
        'user_id' => $user->id, 'order_no' => 'ORD-TEST-'.uniqid(), 'invoice_no' => 'INV-TEST-'.uniqid(),
        'buyer_name' => $user->name, 'buyer_email' => $user->email, 'buyer_whatsapp' => '08123456789',
        'shipping_address_id' => $address->id, 'subtotal' => 200000, 'discount_total' => 0,
        'shipping_cost' => 10000, 'payment_fee' => 0, 'shipping_courier_code' => 'jne',
        'shipping_courier_name' => 'JNE', 'shipping_service_code' => 'REG', 'shipping_service_name' => 'Regular',
        'shipping_etd' => '2-3 hari', 'total' => 210000, 'payment_method' => 'bca_va',
        'status' => 'pending_payment', 'payment_status' => 'pending', 'delivery_status' => 'pending',
        'fulfillment_status' => 'unfulfilled', 'stock_reserved_at' => now(),
    ], $overrides));
    $order->items()->create([
        'product_id' => $variant->product_id, 'product_variant_id' => $variant->id,
        'product_name' => $variant->product->name, 'variant_name' => 'Default', 'sku' => $variant->sku,
        'product_price' => 100000, 'quantity' => 2, 'weight_grams' => 100,
        'stock_reserved' => true, 'subtotal' => 200000,
    ]);
    Payment::create([
        'order_id' => $order->id, 'provider' => 'midtrans', 'provider_order_id' => $order->order_no,
        'payment_type' => 'bca_va', 'status' => 'pending', 'gross_amount' => $order->total,
    ]);
    Invoice::create(['order_id' => $order->id, 'invoice_no' => $order->invoice_no]);

    return compact('order', 'variant');
}

function signedPayload(Order $order, string $status): array
{
    $payload = [
        'order_id' => $order->order_no, 'transaction_status' => $status,
        'transaction_id' => 'TX-'.$order->id, 'status_code' => '200',
        'gross_amount' => number_format($order->total, 2, '.', ''), 'payment_type' => 'bank_transfer',
    ];
    $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('midtrans.server_key'));
    return $payload;
}

beforeEach(function () {
    config(['midtrans.server_key' => 'test-server-key']);
    Queue::fake();
});

test('verified settlement is idempotent and queues one invoice', function () {
    ['order' => $order, 'variant' => $variant] = webhookOrder();
    $payload = signedPayload($order, 'settlement');

    $this->postJson('/api/midtrans/webhook', $payload)->assertOk();
    $this->postJson('/api/midtrans/webhook', $payload)->assertOk();

    expect($order->fresh()->payment_status)->toBe('paid')
        ->and($variant->fresh()->stock_quantity)->toBe(3)
        ->and(Payment::first()->raw_response->offsetExists('signature_key'))->toBeFalse();
    Queue::assertPushed(GenerateAndEmailInvoice::class, 1);
});

test('expired payment releases reserved stock only once', function () {
    ['order' => $order, 'variant' => $variant] = webhookOrder();
    $payload = signedPayload($order, 'expire');

    $this->postJson('/api/midtrans/webhook', $payload)->assertOk();
    $this->postJson('/api/midtrans/webhook', $payload)->assertOk();

    expect($order->fresh()->payment_status)->toBe('expired')
        ->and($variant->fresh()->stock_quantity)->toBe(5);
});

test('invalid midtrans signature is rejected', function () {
    ['order' => $order] = webhookOrder();
    $payload = signedPayload($order, 'settlement');
    $payload['signature_key'] = str_repeat('0', 128);

    $this->postJson('/api/midtrans/webhook', $payload)->assertForbidden();
    expect($order->fresh()->payment_status)->toBe('pending');
});
