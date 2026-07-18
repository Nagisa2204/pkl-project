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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('sku', 255)->unique();
            $table->longtext('description')->nullable();
            $table->unsignedBigInteger('price');
            $table->string('stock_status', 30)->default('avaible');
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('weight_grams');
            $table->unsignedInteger('preorder_days')->default(0);
            $table->unsignedInteger('min_order_quantity')->default(1);
            $table->string('thumbnail', 255)->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};