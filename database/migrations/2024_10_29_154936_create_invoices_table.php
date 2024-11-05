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
            $table->unsignedBigInteger('sale_id');
            $table->string('invoice_number')->unique();
            $table->string('discount_type')->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->date('invoice_due_date')->nullable();
            $table->enum('invoice_frequency', ['Monthly', 'Bi-annually','Annually']);
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->boolean('invoice_active_status')->defaultFalse();
            $table->date('activation_date')->nullable();
            $table->string('month')->nullable();
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete()->constrained()->onDelete('cascade');
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('invoices');
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['marchent_id']);
            // $table->dropForeign(['sale_id']);
        });
    }
};
