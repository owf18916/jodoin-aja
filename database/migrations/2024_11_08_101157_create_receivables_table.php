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
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->index('idx_receivables_bank_id');
            $table->foreignId('customer_id')->index('idx_receivables_customer_id');
            $table->string('invoice_number', 121);
            $table->string('bl_number', 121);
            $table->date('bl_date');
            $table->date('invoice_date');
            $table->date('receipt_date');
            $table->tinyInteger('status')->default(1)->index('idx_receivables_status');
            $table->foreignId('created_by');
            $table->unique(['customer_id', 'invoice_number'], 'unique_receivables');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivables');
    }
};
