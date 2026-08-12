<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            Schema::table('users', fn (Blueprint $table) => $table->string('phone', 30)->nullable()->change());
        }

        $legacyProducts = Schema::hasColumn('products', 'sku');

        if ($legacyProducts) {
            DB::table('products')->orderBy('id')->chunkById(100, function ($products): void {
                foreach ($products as $product) {
                    DB::table('product_variants')->updateOrInsert(
                        ['product_id' => $product->id, 'combination_key' => 'default'],
                        [
                            'sku' => $product->sku,
                            'price' => $product->price,
                            'compare_at_price' => null,
                            'stock_quantity' => $product->stock_quantity,
                            'stock_status' => $product->stock_status,
                            'weight_grams' => $product->weight_grams,
                            'preorder_days' => $product->preorder_days,
                            'is_default' => true,
                            'is_active' => $product->is_active,
                            'created_at' => $product->created_at,
                            'updated_at' => now(),
                        ]
                    );

                    if ($product->thumbnail && ! DB::table('product_images')->where('product_id', $product->id)->where('image_path', $product->thumbnail)->exists()) {
                        DB::table('product_images')->insert([
                            'product_id' => $product->id,
                            'image_path' => $product->thumbnail,
                            'sort_order' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            });
        }

        if (! Schema::hasColumn('product_images', 'alt_text')) {
            Schema::table('product_images', function (Blueprint $table) {
                $table->string('alt_text')->nullable()->after('image_path');
                $table->boolean('is_primary')->default(false)->after('alt_text');
                $table->index(['product_id', 'is_primary']);
            });

            DB::table('product_images')->select('product_id', DB::raw('MIN(id) as image_id'))
                ->groupBy('product_id')->get()->each(
                    fn ($row) => DB::table('product_images')->where('id', $row->image_id)->update(['is_primary' => true])
                );
        }

        if (! Schema::hasColumn('cart_items', 'product_variant_id')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->foreignId('product_variant_id')->nullable()->after('product_id')
                    ->constrained('product_variants')->cascadeOnDelete();
            });
            DB::table('cart_items')->orderBy('id')->eachById(function ($item): void {
                $variantId = DB::table('product_variants')->where('product_id', $item->product_id)->where('is_default', true)->value('id');
                DB::table('cart_items')->where('id', $item->id)->update(['product_variant_id' => $variantId]);
            });
            Schema::table('cart_items', fn (Blueprint $table) => $table->dropConstrainedForeignId('product_id'));
        }

        if (! Schema::hasColumn('order_items', 'product_variant_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->restrictOnDelete();
                $table->string('variant_name')->nullable()->after('product_name');
                $table->json('variant_options')->nullable()->after('variant_name');
                $table->boolean('stock_reserved')->default(true)->after('weight_grams');
            });
            DB::table('order_items')->orderBy('id')->eachById(function ($item): void {
                $variantId = DB::table('product_variants')->where('product_id', $item->product_id)->where('is_default', true)->value('id');
                DB::table('order_items')->where('id', $item->id)->update([
                    'product_variant_id' => $variantId,
                    'variant_name' => 'Default',
                ]);
            });
        }

        if (! Schema::hasColumn('orders', 'order_no')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('order_no', 64)->nullable()->after('id');
                $table->unsignedBigInteger('discount_total')->default(0)->after('subtotal');
                $table->unsignedBigInteger('payment_fee')->default(0)->after('shipping_cost');
                $table->string('payment_method', 80)->default('midtrans_snap')->after('total');
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamp('completed_at')->nullable();
            });
            DB::table('orders')->orderBy('id')->eachById(fn ($order) =>
                DB::table('orders')->where('id', $order->id)->update(['order_no' => 'ORD-LEGACY-'.$order->id])
            );
            Schema::table('orders', fn (Blueprint $table) => $table->unique('order_no'));
        }

        if (! Schema::hasColumn('payments', 'fraud_status')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('fraud_status', 30)->nullable()->after('status');
                $table->string('status_code', 10)->nullable()->after('fraud_status');
                $table->index(['order_id', 'status']);
            });
        }

        if (! Schema::hasColumn('shipments', 'province_name')) {
            Schema::table('shipments', function (Blueprint $table) {
                $table->string('province_name')->nullable()->after('address_line');
                $table->string('city_name')->nullable()->after('province_name');
                $table->string('district_name')->nullable()->after('city_name');
                $table->string('subdistrict_name')->nullable()->after('district_name');
                $table->string('postal_code', 20)->nullable()->after('subdistrict_name');
            });
            DB::table('shipments')->orderBy('id')->eachById(function ($shipment): void {
                $address = DB::table('user_addresses')->find($shipment->user_address_id);
                if ($address) {
                    DB::table('shipments')->where('id', $shipment->id)->update([
                        'province_name' => $address->province_name,
                        'city_name' => $address->city_name,
                        'district_name' => $address->district_name,
                        'subdistrict_name' => $address->subdistrict_name,
                        'postal_code' => $address->postal_code,
                    ]);
                }
            });
        }

        if ($legacyProducts) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
                $table->dropColumn(['sku', 'price', 'stock_status', 'stock_quantity', 'weight_grams', 'preorder_days', 'thumbnail']);
            });
        }

        if (Schema::hasColumn('product_images', 'caption')) {
            Schema::table('product_images', fn (Blueprint $table) => $table->dropColumn('caption'));
        }

        if (Schema::hasColumn('order_items', 'stock_status')) {
            Schema::table('order_items', fn (Blueprint $table) => $table->dropColumn('stock_status'));
        }
    }

    public function down(): void
    {
        // This migration intentionally has no destructive rollback because it migrates live commerce data.
    }
};
