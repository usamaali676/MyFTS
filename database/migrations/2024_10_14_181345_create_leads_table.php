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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('saler_id');
            $table->string('business_name_adv');
            $table->string('business_number_adv');
            $table->string('off_email')->nullable();
            $table->string('website_url')->nullable();
            $table->enum( 'lead_status', ['Interested', 'Do Not Caller List', 'Do Not Intrested' ]);
            $table->enum('call_status', ['Interested', 'Do Not Caller List', 'Asked to Callback', 'No Picked',  'Picked']);
            $table->dateTime('call_back_time');
            $table->foreign('category_id')->references('id')->on('business_categories');
            $table->softDeletes();
            $table->foreign('saler_id')->references('id')->on('users');
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
        Schema::dropIfExists('leads');
    }
};
