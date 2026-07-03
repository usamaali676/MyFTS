<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteReportDetail extends Model
{
    protected $fillable = [
        'client_reporting_id',
        'keywordCount',
        'keywordFirstpage',
        'keywordSecondpage',
        'avg_pages_position',
        'improveWebsiteSpeed',
        'seoMetaTags',
        'optimizedurl',
        'googleSearchConsole',
        'titleOptimized',
        'headingTags',
        'metaDescription',
        'loadingSpeed',
        'imageAltTags',
        'schemaMarkup',
        'robotTxt',
        'xmlSitemap',
        'indexOptimization',
        'websiteUrlsCount',
        'websiteUrls',
        'socialBookmarking',
        'socialMediaSharing',
        'internalLinks',
        'backlinksCount',
        'blogBacklinks',
        'bookmark_backlinks',
        'total_impressions',
        'total_clicks',
        'avg_ctr',
        'experience_score',
        'expertise_score',
        'authority_score',
        'trust_score',
        'lcp',
        'inp',
        'cls',
        'fcp',
        'ttfb',
        'page_speed',
    ];

    protected $casts = [
        'googleSearchConsole' => 'boolean',
        'schemaMarkup' => 'boolean',
        'robotTxt' => 'boolean',
        'improveWebsiteSpeed' => 'integer',
        'seoMetaTags' => 'integer',
        'optimizedurl' => 'integer',
        'titleOptimized' => 'integer',
        'headingTags' => 'integer',
        'metaDescription' => 'integer',
        'imageAltTags' => 'integer',
        'indexOptimization' => 'integer',
        'socialBookmarking' => 'integer',
    ];

    public function report()
    {
        return $this->belongsTo(ClientReporting::class, 'client_reporting_id');
    }
}
