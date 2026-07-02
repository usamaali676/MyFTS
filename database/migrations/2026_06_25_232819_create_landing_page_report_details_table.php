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
        Schema::create('landing_page_report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_reporting_id')->constrained('client_reportings')->onDelete('cascade');
            $table->bigInteger('keywords_count')->nullable();
            $table->text('first_page_keywords')->nullable();
            $table->text('second_page_keywords')->nullable();
            $table->bigInteger('backlinks_count')->nullable();
            $table->text('blog_backlinks')->nullable();
            $table->text('bookmark_backlinks')->nullable();
            $table->bigInteger('social_bookmark_count')->nullable();
            $table->bigInteger('landing_page_count')->nullable();
            $table->text('landing_page_urls')->nullable();
            $table->bigInteger('avg_pages_position')->nullable();
            $table->bigInteger('total_impressions')->nullable();
            $table->bigInteger('total_clicks')->nullable();
            $table->DECIMAL('avg_ctr', 5, 2)->nullable();
            $table->bigInteger('experience_score')->nullable();
            $table->bigInteger('expertise_score')->nullable();
            $table->bigInteger('authority_score')->nullable();
            $table->bigInteger('trust_score')->nullable();
            $table->bigInteger('internal_links_count')->nullable();
            // Core Web Vitals
            $table->decimal('lcp', 8, 2)->nullable();
            $table->decimal('inp', 8, 2)->nullable();
            $table->decimal('cls', 8, 2)->nullable();
            $table->decimal('fcp', 8, 2)->nullable();
            $table->decimal('ttfb', 8, 2)->nullable();
            $table->bigInteger('page_speed')->nullable();
            $table->bigInteger('social_media_shares')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_report_details');
    }
};
