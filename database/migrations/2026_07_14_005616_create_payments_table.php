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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('restrict');
            $table->string('provider', 255);
            $table->string('provider_order_id', 255)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->string('payment_type', 255);
            $table->string('bank', 255)->nullable();
            $table->string('va_number', 255)->nullable();
            $table->string('bill_key', 255)->nullable();
            $table->string('biller_code', 255)->nullable();
            $table->string('status', 255);
            $table->unsignedBigInteger('gross_amount');
            $table->unsignedBigInteger('refunded_amount')->default(0);
            $table->string('snap_token', 255)->nullable();
            $table->string('redirect_url', 255)->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamp('expiry_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
