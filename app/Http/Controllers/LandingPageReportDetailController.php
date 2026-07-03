<?php

namespace App\Http\Controllers;

use App\Models\ClientReporting;
use App\Models\LandingPageReportDetail;
use Illuminate\Http\Request;

class LandingPageReportDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
     public function storeOrUpdate(Request $request, $reportId)
    {
        $request->validate([
            'keywods_count' => 'nullable|integer',
            'first_page_keywords' => 'nullable|string',
            'second_page_keywords' => 'nullable|string',
            'backlinks_count' => 'nullable|integer',
            'blog_backlinks' => 'nullable|string',
            'bookmark_backlinks' => 'nullable|string',
            'social_bookmark_count' => 'nullable|integer',
            'landing_page_urls' => 'nullable|string',

            'avg_pages_position' => 'nullable|integer',
            'total_impressions' => 'nullable|integer',
            'total_clicks' => 'nullable|integer',
            'avg_ctr' => 'nullable|integer',

            'experience_score' => 'nullable|integer',
            'expertise_score' => 'nullable|integer',
            'authority_score' => 'nullable|integer',
            'trust_score' => 'nullable|integer',

            'internal_links_count' => 'nullable|integer',

            'lcp' => 'nullable|integer',
            'inp' => 'nullable|integer',
            'cls' => 'nullable|integer',
            'fcp' => 'nullable|integer',
            'ttfb' => 'nullable|integer',

            'page_speed' => 'nullable|integer',
            'social_media_shares' => 'nullable|integer',
        ]);

        $report = ClientReporting::findOrFail($reportId);

        $report->landingPage()->updateOrCreate(
            ['client_reporting_id' => $report->id],
            $request->all()
        );

        return back()->with('success', 'Landing page report saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LandingPageReportDetail $landingPageReportDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LandingPageReportDetail $landingPageReportDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LandingPageReportDetail $landingPageReportDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LandingPageReportDetail $landingPageReportDetail)
    {
        //
    }
}
