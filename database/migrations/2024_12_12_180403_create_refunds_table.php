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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('lead_id');
            $table->enum('refund_type', ['Full', 'Partial']);
            $table->decimal('refund_amount', 10, 2);
            $table->date('claim_date')->nullable();
            $table->unsignedBigInteger('merchant_id');
            $table->text('claim_reason')->nullable();
            $table->foreign('payment_id')->references('id')->on('payments');
            $table->foreign('merchant_id')->references('id')->on('merchant_accounts');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('lead_id')->references('id')->on('leads');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
