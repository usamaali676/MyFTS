<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\ClientReporting;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientReportingController extends Controller
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
        // dd($request->all());
        try {
            $request->validate([
                'report_type' => 'required',
                'client_id' =>'required',
                'created_by' => 'required'
            ]);
            $report = ClientReporting::create([
                'client_id' => $request->client_id,
                'report_type' => $request->report_type,
                'created_by' => $request->created_by,
                'status' => 'created',
                'created_at' => Carbon::now(),
                 'report_month' => Carbon::now()->monthName,
                 'report_year' => Carbon::now()->year,
                  'uuid'  => Str::uuid(),
            ]);
            $reports = ClientReporting::where('client_id', $request->client_id)->with('client', 'client.sale.lead', 'createdBy', 'verifiedBy', 'dispatchedBy')->get();
            switch ($request->report_type) {
                case 'landing_page':
                    // dd($request->all());
                            $request->validate([
                            'keywords_count' => 'nullable|integer',
                            'first_page_keywords' => 'nullable|string',
                            'second_page_keywords' => 'nullable|string',
                            'backlinks_count' => 'nullable|integer',
                            'blog_backlinks' => 'nullable|string',
                            'bookmark_backlinks' => 'nullable|string',
                            'social_bookmark_count' => 'nullable|integer',
                            'landing_page_urls' => 'nullable|string',
                            'landing_page_count' => 'nullable|string',
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
                        $report->landingPage()->updateOrCreate(
                            ['client_reporting_id' => $report->id],
                            $request->all()
                        );

                    break;

                case 'website':
                    $request->validate([
                        'keywordCount' => 'nullable|integer',
                        'keywordFirstpage' => 'nullable|string',
                        'keywordSecondpage' => 'nullable|string',
                        'avg_pages_position' => 'nullable|integer',
                        'improveWebsiteSpeed' => 'nullable|integer|min:0|max:100',
                        'seoMetaTags' => 'nullable|integer|min:0|max:100',
                        'optimizedurl' => 'nullable|integer|min:0|max:100',
                        'googleSearchConsole' => 'nullable|boolean',
                        'titleOptimized' => 'nullable|integer|min:0|max:100',
                        'headingTags' => 'nullable|integer|min:0|max:100',
                        'metaDescription' => 'nullable|integer|min:0|max:100',
                        'loadingSpeed' => 'nullable|string|max:20',
                        'imageAltTags' => 'nullable|integer|min:0|max:100',
                        'schemaMarkup' => 'nullable|boolean',
                        'robotTxt' => 'nullable|boolean',
                        'xmlSitemap' => 'nullable|string|max:50',
                        'indexOptimization' => 'nullable|integer|min:0|max:100',
                        'websiteUrlsCount' => 'nullable|integer',
                        'websiteUrls' => 'nullable|string',
                        'socialBookmarking' => 'nullable|integer',
                        'socialMediaSharing' => 'nullable|integer',
                        'internalLinks' => 'nullable|integer',
                        'backlinksCount' => 'nullable|integer',
                        'blogBacklinks' => 'nullable|string',
                        'bookmark_backlinks' => 'nullable|string',
                        'total_impressions' => 'nullable|integer',
                        'total_clicks' => 'nullable|integer',
                        'avg_ctr' => 'nullable|numeric',
                        'experience_score' => 'nullable|integer',
                        'expertise_score' => 'nullable|integer',
                        'authority_score' => 'nullable|integer',
                        'trust_score' => 'nullable|integer',
                        'lcp' => 'nullable|numeric',
                        'inp' => 'nullable|numeric',
                        'cls' => 'nullable|numeric',
                        'fcp' => 'nullable|numeric',
                        'ttfb' => 'nullable|numeric',
                        'page_speed' => 'nullable|integer',
                    ]);
                    $report->website()->updateOrCreate(
                        ['client_reporting_id' => $report->id],
                        $request->all()
                    );
                    break;

                case 'gmb':
                    $report->gmb()->create([
                        'client_reporting_id' => $report->id,
                    ]);
                    break;
            }

            return response()->json([
               'message' => 'Report created successfully.',
                'reports' => $reports
            ], 201);
        } catch (Exception $e) {
            return response()->json([
               'message' => 'An error occurred while creating the report.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
         $report = ClientReporting::where('uuid', $uuid)->with(['landingPage', 'website'])->firstOrFail();
            $lastMonth = Carbon::now()->subMonth();
            $last_report = ClientReporting::where('client_id', $report->client_id)
                ->where('report_month', $lastMonth->month)
                ->where('report_year', $lastMonth->year)
                ->first();        //  dd($report);
                $firstPageKeywords = $report->report_type === 'website'
                    ? $report->website->keywordFirstpage
                    : $report->landingpage->first_page_keywords;
                preg_match_all('/<li>(.*?)<\/li>/is', $firstPageKeywords, $matches);
    $keywords = array_map(
        fn($item) => html_entity_decode(strip_tags(trim($item))),
        $matches[1]
    );

    // 2. Extract service phrase (strip location)
    $servicePhrases = array_map(function ($keyword) {
    // Remove everything from " in " onward
    if (preg_match('/^(.+?)\s+(?:in|near|for)\s+.+$/i', $keyword, $m)) {
        return strtolower(trim($m[1]));
    }
    // Fallback: strip last 2 words (assumed to be city + state)
    $words = preg_split('/\s+/', trim($keyword));
    if (count($words) > 2) {
        $words = array_slice($words, 0, -2);
    }
    return strtolower(implode(' ', $words));
    }, $keywords);

    // 3. Normalize: remove filler adjectives to get the core service
    $fillers = [
        'professional', 'best', 'affordable', 'residential', 'commercial',
        'custom', 'advanced', 'remote', 'expert', 'service', 'services',
        'installation', 'installer', 'install', 'setup', 'company',
        'contractor', 'contractors', 'cost', 'pricing', 'how to',
        'mounting', 'mount', 'mounted',
    ];

    $normalized = array_map(function ($phrase) use ($fillers) {
        $words = preg_split('/\s+/', $phrase);
        $words = array_filter($words, fn($w) => !in_array($w, $fillers));
        return implode(' ', array_values($words));
    }, $servicePhrases);

    // 4. Group by similarity using the first 2 meaningful words as the bucket key
    $groups = [];
    foreach ($normalized as $i => $norm) {
        if (empty(trim($norm))) continue;

        $words = preg_split('/\s+/', trim($norm));
        // Use up to 2 words as the group key
        $key = implode(' ', array_slice($words, 0, 2));

        if (!isset($groups[$key])) {
            $groups[$key] = ['count' => 0, 'label' => $key];
        }
        $groups[$key]['count']++;
    }

    // 5. Sort descending by count
    uasort($groups, fn($a, $b) => $b['count'] <=> $a['count']);
    $groups = array_slice($groups, 0, 6, true);

    // 6. Build chart arrays
    $chartLabels = array_map(fn($g) => ucwords($g['label']), array_values($groups));
    $chartData   = array_map(fn($g) => $g['count'], array_values($groups));

        $view = $report->report_type === 'website'
            ? 'pages.clientReport.websiteReport'
            : 'pages.clientReport.landingPageReport';

            // dd($view);

        return view($view, compact('report', 'last_report', 'chartLabels', 'chartData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $client_report = ClientReporting::find($id);
        $user = Auth::user();
        if(isset($client_report) && $client_report->verified_by != NULL){
            return response()->json([
                'error' => 'Report is Aready Verified!',
            ], 409);
        }
        else{
            $client_report->update([
                'verified_by' => $user->id,
                'report_verified_at' => Carbon::now(),
                'report_status' => 'verified',
            ]);
            $all_reports = ClientReporting::where('client_id', $client_report->client_id)->with('client', 'client.sale.lead', 'createdBy', 'verifiedBy', 'dispatchedBy')->get();
            return response()->json([
                'message' => 'Report Verified Successfully!',
                'reports' => $all_reports,
            ], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $client_report = ClientReporting::find($id);
        // dd($client_report);
        if($client_report->report_status == "created"){
            return response()->json([
                'error' => 'Can not Dispatch Without Varifications',
            ], 409);
        }
        else{
            $user = Auth::user();
            if(isset($client_report) && $client_report->dispatch_at != NULL){
                return response()->json([
                    'error' => 'Report is Already Dispatched!',
                ], 409);
            }
            else{
                $client_report->update([
                    'dispatched_by' => $user->id,
                    'dispatch_at' => Carbon::now(),
                    'report_status' => 'dispatched',
                ]);
                $all_reports = ClientReporting::where('client_id', $client_report->client_id)->with('client', 'client.sale.lead', 'createdBy', 'verifiedBy', 'dispatchedBy')->get();
                return response()->json([
                    'message' => 'Report Verified Successfully!',
                    'reports' => $all_reports,
                ], 200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $clientReport = ClientReporting::find($request->id);
        $clientReport->delete();

        // $rem_keyword = K
        return response()->json([
           'message' => 'Report deleted successfully!'
        ], 200);
    }
}
