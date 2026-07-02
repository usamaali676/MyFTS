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
        Schema::create('website_report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_reporting_id')->constrained('client_reportings')->onDelete('cascade');

            $table->bigInteger('keywordCount')->nullable();
            $table->text('keywordFirstpage')->nullable();
            $table->text('keywordSecondpage')->nullable();
            $table->bigInteger('avg_pages_position')->nullable();

            $table->unsignedTinyInteger('improveWebsiteSpeed')->nullable();
            $table->unsignedTinyInteger('seoMetaTags')->nullable();
            $table->unsignedTinyInteger('optimizedurl')->nullable();
            $table->boolean('googleSearchConsole')->nullable();
            $table->unsignedTinyInteger('titleOptimized')->nullable();
            $table->unsignedTinyInteger('headingTags')->nullable();
            $table->unsignedTinyInteger('metaDescription')->nullable();
            $table->string('loadingSpeed', 20)->nullable();
            $table->unsignedTinyInteger('imageAltTags')->nullable();
            $table->boolean('schemaMarkup')->nullable();
            $table->boolean('robotTxt')->nullable();
            $table->string('xmlSitemap', 50)->nullable();
            $table->unsignedTinyInteger('indexOptimization')->nullable();

            $table->bigInteger('websiteUrlsCount')->nullable();
            $table->text('websiteUrls')->nullable();

            $table->bigInteger('socialBookmarking')->nullable();
            $table->bigInteger('socialMediaSharing')->nullable();
            $table->bigInteger('internalLinks')->nullable();

            $table->bigInteger('backlinksCount')->nullable();
            $table->text('blogBacklinks')->nullable();
            $table->text('bookmark_backlinks')->nullable();

            $table->bigInteger('total_impressions')->nullable();
            $table->bigInteger('total_clicks')->nullable();
            $table->decimal('avg_ctr', 5, 2)->nullable();

            $table->bigInteger('experience_score')->nullable();
            $table->bigInteger('expertise_score')->nullable();
            $table->bigInteger('authority_score')->nullable();
            $table->bigInteger('trust_score')->nullable();

            $table->decimal('lcp', 8, 2)->nullable();
            $table->decimal('inp', 8, 2)->nullable();
            $table->decimal('cls', 8, 2)->nullable();
            $table->decimal('fcp', 8, 2)->nullable();
            $table->decimal('ttfb', 8, 2)->nullable();
            $table->bigInteger('page_speed')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_report_details');
    }
};
