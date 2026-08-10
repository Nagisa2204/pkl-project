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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_variant_id')->constrained('product_variants')->restrictOnDelete();
            $table->string('product_name', 255);
            $table->string('variant_name', 255)->nullable();
            $table->json('variant_options')->nullable();
            $table->string('sku', 255)->index();
            $table->unsignedBigInteger('product_price');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('weight_grams');
            $table->boolean('stock_reserved')->default(true);
            $table->unsignedBigInteger('subtotal');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
