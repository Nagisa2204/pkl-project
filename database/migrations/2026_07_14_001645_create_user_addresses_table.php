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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('label', 255);
            $table->string('receiver_name', 255);
            $table->string('phone', 30)->nullable();
            $table->text('address_line');
            $table->string('province_name', 255);
            $table->string('city_name', 255);
            $table->string('district_name', 255);
            $table->string('subdistrict_name', 255);
            $table->string('postal_code', 20);
            $table->text('courier_note')->nullable();
            $table->unsignedBigInteger('destination_id');
            $table->string('destination_label', 255);
            $table->boolean('is_default')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
