<?php

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\OrderDetail;
use App\Models\Order;
use App\Models\User;
use App\Models\UserAddress;
use Livewire\Livewire;

test('order detail and admin dashboard render with one Livewire root element', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $address = UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Rumah',
        'receiver_name' => $user->name,
        'phone' => '08123456789',
        'address_line' => 'Jalan Uji',
        'province_name' => 'Nusa Tenggara Barat',
        'city_name' => 'Mataram',
        'district_name' => 'Sekarbela',
        'subdistrict_name' => 'Tanjung Karang',
        'postal_code' => '83115',
        'destination_id' => 202,
        'destination_label' => 'Tanjung Karang, Mataram',
        'is_default' => true,
    ]);
    $order = Order::create([
        'user_id' => $user->id,
        'order_no' => 'ORD-ROOT-1',
        'invoice_no' => 'INV-ROOT-1',
        'buyer_name' => $user->name,
        'buyer_email' => $user->email,
        'buyer_whatsapp' => '08123456789',
        'shipping_address_id' => $address->id,
        'subtotal' => 100000,
        'discount_total' => 0,
        'shipping_cost' => 10000,
        'payment_fee' => 0,
        'shipping_courier_code' => 'jne',
        'shipping_courier_name' => 'JNE',
        'shipping_service_code' => 'REG',
        'shipping_service_name' => 'Reguler',
        'shipping_etd' => '2-3 hari',
        'total' => 110000,
        'payment_method' => 'midtrans_snap',
        'status' => 'pending_payment',
        'payment_status' => 'pending',
        'delivery_status' => 'pending',
        'fulfillment_status' => 'unfulfilled',
    ]);

    Livewire::actingAs($user)
        ->test(OrderDetail::class, ['invoice_no' => $order->invoice_no])
        ->assertStatus(200);

    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Livewire::actingAs($admin)->test(AdminDashboard::class)->assertStatus(200);
});
