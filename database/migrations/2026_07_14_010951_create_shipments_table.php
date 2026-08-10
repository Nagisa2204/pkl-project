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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_address_id')->constrained();
            $table->string('receiver_name', 255);
            $table->string('phone', 30);
            $table->text('address_line');
            $table->string('province_name');
            $table->string('city_name');
            $table->string('district_name');
            $table->string('subdistrict_name');
            $table->string('postal_code', 20);
            $table->text('courier_note')->nullable();
            $table->unsignedBigInteger('destination_id');
            $table->string('destination_label', 255);
            $table->string('courier_code', 40);
            $table->string('courier_name', 255);
            $table->string('service_code', 80);
            $table->string('service_name', 255);
            $table->unsignedBigInteger('cost');
            $table->string('etd', 255);
            $table->string('awb_number', 255)->nullable()->index();
            $table->string('status', 30)->index();
            $table->json('raw_response')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->unique('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
