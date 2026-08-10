<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'payment_method')) {
            return;
        }

        DB::table('orders')
            ->where('payment_method', 'all')
            ->where('payment_status', 'pending')
            ->update(['payment_method' => 'midtrans_snap']);
    }

    public function down(): void
    {
        // Nilai historis tidak dikembalikan karena metode lama tidak lagi dipilih di checkout.
    }
};
