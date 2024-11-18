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
            $table->string('card_number');
            $table->string('paypal_email');

            $table->decimal('amount', 10, 2);
            $table->decimal('balance', 10, 2)->default(0);
            $table->char('trans_id')->nullable();
            $table->string('trans_ss')->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchant_accounts')->onDelete()->constrained()->onDelete('cascade');
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
