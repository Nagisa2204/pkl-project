<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('shipments')->where('status', 'processing')->update(['status' => 'packing']);
        DB::table('orders')->where('delivery_status', 'processing')->update(['delivery_status' => 'packing']);
    }

    public function down(): void
    {
        DB::table('shipments')->where('status', 'packing')->update(['status' => 'processing']);
        DB::table('orders')->where('delivery_status', 'packing')->update(['delivery_status' => 'processing']);
    }
};
