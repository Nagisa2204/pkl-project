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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->unique()->onDelete('restrict');
            $table->string('invoice_no', 255)->unique()->index();
            $table->string('file_path', 255)->nullable();
            $table->timestamp("generated_at")->nullable();
            $table->timestamp("emailed_at")->nullable();
            $table->timestamp("email_sending_at")->nullable();
            $table->timestamp("admin_emailed_at")->nullable();
            $table->timestamp("admin_email_sending_at")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
