<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('district')->nullable();
            $table->string('subdistrict')->nullable();
            $table->unsignedBigInteger('shipping_province_id')->nullable();
            $table->unsignedBigInteger('shipping_city_id')->nullable();
            $table->unsignedBigInteger('shipping_district_id')->nullable();
            $table->unsignedBigInteger('shipping_origin_id')->nullable()->index();
            $table->string('shipping_origin_label')->nullable();
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->unsignedBigInteger('origin_id')->nullable()->index();
            $table->string('origin_label')->nullable();
            $table->text('origin_address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['origin_id', 'origin_label', 'origin_address']);
        });

        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'district', 'subdistrict', 'shipping_province_id', 'shipping_city_id',
                'shipping_district_id', 'shipping_origin_id', 'shipping_origin_label',
            ]);
        });
    }
};
