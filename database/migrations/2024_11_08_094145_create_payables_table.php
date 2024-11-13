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
        Schema::create('payables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->index('idx_payables_supplier_id');
            $table->string('invoice_number', 121);
            $table->date('accounted_date');
            $table->foreignId('currency_id')->index('idx_payables_currency_id');
            $table->decimal('amount', 12, 2);
            $table->tinyInteger('status')->default(1)->index('idx_payables_status');
            $table->foreignId('created_by');
            $table->unique(['supplier_id', 'invoice_number'], 'unique_payables');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payables');
    }
};
