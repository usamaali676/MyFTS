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
            $table->unsignedBigInteger('client_id');
            $table->enum('reporting_type', ['Landing Pages', 'SMM', 'GMB','Website Development', 'Other']);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->unsignedBigInteger('dispatched_by')->nullable();
            $table->string('report_file');
            $table->enum('report_status',['created', 'verified','dispatched']);
            $table->timestamp('report_verified_at')->nullable();
            $table->timestamp('dispatch_at')->nullable();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('dispatched_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_reportings');
    }
};
