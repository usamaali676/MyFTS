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
        Schema::create('company_services_lead', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_services_id');
            $table->unsignedBigInteger('lead_id');
            $table->foreign('company_services_id')->references('id')->on('company_services');
            $table->foreign('lead_id')->references('id')->on('leads');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_services_lead');
    }
};
