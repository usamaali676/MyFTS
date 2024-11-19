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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->string('invoice_number');
            $table->unsignedBigInteger('merchant_id');
            $table->enum('mop', ['Credit Card','PayPal','Zeele','Cash App','Bank Transfer', 'other'])->default('other');
            $table->enum('payment_type', ['Full Payment', 'Partials Payment', 'Advance Payment'])->default('Full Payment');
            $table->string('card_number')->nullable();
            $table->string('paypal_email')->nullable();
            $table->unsignedBigInteger('cashapp_id')->nullable();
            $table->unsignedBigInteger('zelle_id')->nullable();
            $table->unsignedBigInteger('bank_transfer_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('balance', 10, 2)->default(0);
            $table->char('trans_id')->nullable();
            $table->string('trans_ss')->nullable();
            $table->foreign('cashapp_id')->references('id')->on('cashapps');
            $table->foreign('zelle_id')->references('id')->on('zelle_accounts');
            $table->foreign('bank_transfer_id')->references('id')->on('bank_accounts');
            $table->foreign('merchant_id')->references('id')->on('merchant_accounts');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
