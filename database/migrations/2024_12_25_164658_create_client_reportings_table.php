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
        Schema::create('client_reportings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('client_id')->constrained();
            $table->enum('report_type', [
                'website',
                'landing_page',
                'gmb',
                'smm',
            ]);
            $table->string('report_month');
            $table->string('report_year');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->unsignedBigInteger('dispatched_by')->nullable();
            $table->enum('status', [
                'draft',
                'verified',
                'dispatched'
            ])->default('draft');
            $table->timestamp('report_verified_at')->nullable();
            $table->timestamp('dispatch_at')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('dispatched_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
            $table->unique(
                ['client_id', 'report_type', 'report_month', 'report_year'],
                'cr_report_unique'
            );        });
    }
    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::dropIfExists('client_reportings');
}
};
