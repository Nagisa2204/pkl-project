<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('order_no', 64)->unique();
            $table->string('invoice_no', 64)->unique();
            $table->string('buyer_name', 255);
            $table->string('buyer_email', 255);
            $table->string('buyer_whatsapp', 255);
            $table->foreignId('shipping_address_id')->constrained('user_addresses');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('discount_total')->default(0);
            $table->unsignedBigInteger('shipping_cost');
            $table->unsignedBigInteger('payment_fee')->default(0);
            $table->string('shipping_courier_code', 40);
            $table->string('shipping_courier_name', 255);
            $table->string('shipping_service_code', 80);
            $table->string('shipping_service_name', 255);
            $table->string('shipping_etd', 255);
            $table->unsignedBigInteger('total');
            $table->string('payment_method', 80);
            $table->string('status', 255)->index();
            $table->string('payment_status', 255)->index();
            $table->string('delivery_status', 255);
            $table->string('fulfillment_status', 30);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('stock_reserved_at')->nullable();
            $table->timestamp('stock_released_at')->nullable();
            $table->timestamp('stock_shortage_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
