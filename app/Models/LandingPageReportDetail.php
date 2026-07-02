<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageReportDetail extends Model
{
    protected $fillable = [
        'client_reporting_id',
        'keywords_count',
        'first_page_keywords',
        'second_page_keywords',
        'backlinks_count',
        'blog_backlinks',
        'bookmark_backlinks',
        'social_bookmark_count',
        'landing_page_urls',
        'landing_page_count',
        'avg_pages_position',
        'total_impressions',
        'total_clicks',
        'avg_ctr',
        'experience_score',
        'expertise_score',
        'authority_score',
        'trust_score',
        'internal_links_count',
        'lcp',
        'inp',
        'cls',
        'fcp',
        'ttfb',
        'page_speed',
        'social_media_shares',
    ];

    public function report()
    {
        return $this->belongsTo(ClientReporting::class, 'client_reporting_id');
    }
}
