<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * These columns hold on-page SEO scores (e.g. "Title Optimized: 100%") and
     * are being converted from free text to a 0-100 percentage integer.
     */
    private array $percentColumns = [
        'improveWebsiteSpeed',
        'seoMetaTags',
        'optimizedurl',
        'titleOptimized',
        'headingTags',
        'metaDescription',
        'imageAltTags',
        'indexOptimization',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Any pre-existing non-numeric values (e.g. placeholder text entered
        // before the column types were corrected) can't survive the numeric
        // conversion below, so blank them out rather than fail the migration.
        foreach ($this->percentColumns as $column) {
            DB::table('website_report_details')
                ->whereRaw("`{$column}` NOT REGEXP '^[0-9]+$'")
                ->update([$column => null]);
        }

        DB::table('website_report_details')
            ->whereRaw("`socialBookmarking` NOT REGEXP '^[0-9]+$'")
            ->update(['socialBookmarking' => null]);

        Schema::table('website_report_details', function (Blueprint $table) {
            foreach ($this->percentColumns as $column) {
                $table->unsignedTinyInteger($column)->nullable()->change();
            }

            $table->string('loadingSpeed')->nullable()->change();
            $table->string('xmlSitemap')->nullable()->change();
            $table->bigInteger('socialBookmarking')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_report_details', function (Blueprint $table) {
            foreach ($this->percentColumns as $column) {
                $table->text($column)->nullable()->change();
            }

            $table->text('loadingSpeed')->nullable()->change();
            $table->text('xmlSitemap')->nullable()->change();
            $table->text('socialBookmarking')->nullable()->change();
        });
    }
};
