@extends('layouts.dashboard')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<style>
    .report-section-title {
        letter-spacing: 0;
    }
</style>
@endsection

@section('content')
@php
    $isWebsite = $report->report_type === 'website';
    $detail = $isWebsite ? $report->website : $report->landingPage;
    $clientName = optional(optional(optional($report->client)->sale)->lead)->business_name_adv ?? 'Client';
    $fieldValue = function ($name) use ($detail) {
        return old($name, $detail->{$name} ?? null);
    };
    $textareaValue = function ($name) use ($fieldValue) {
        return $fieldValue($name) ?? '';
    };
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="py-3 mb-1">
                <span class="text-muted fw-light">Client Report /</span> Edit
            </h4>
            <div class="text-muted">{{ $clientName }} - {{ $isWebsite ? 'Website Development' : 'Landing Page' }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reportShow', $report->uuid) }}" target="_blank" class="btn btn-outline-primary">
                <i class="mdi mdi-eye me-1"></i>Preview
            </a>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="report_edit" method="POST" action="{{ route('clientReport.saveReport', $report->id) }}">
        @csrf

        <div class="card">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" value="{{ $isWebsite ? 'Website Development' : 'Landing Page' }}" readonly>
                            <label>Report Type</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" value="{{ $report->report_month }} {{ $report->report_year }}" readonly>
                            <label>Report Month</label>
                        </div>
                    </div>

                    @if(! $isWebsite)
                        <div class="col-12">
                            <h6 class="text-muted fw-semibold text-uppercase mb-0 report-section-title">SEO &amp; Keywords</h6>
                            <hr class="mt-1">
                        </div>
                        @include('pages.clientReport.partials.report-input', ['name' => 'keywords_count', 'label' => 'Keywords Count', 'type' => 'number', 'value' => $fieldValue('keywords_count')])
                        @include('pages.clientReport.partials.report-textarea', ['name' => 'first_page_keywords', 'label' => 'First Page Keywords', 'value' => $textareaValue('first_page_keywords')])
                        @include('pages.clientReport.partials.report-textarea', ['name' => 'second_page_keywords', 'label' => 'Second Page Keywords', 'value' => $textareaValue('second_page_keywords')])

                        <div class="col-12 mt-2">
                            <h6 class="text-muted fw-semibold text-uppercase mb-0 report-section-title">Backlinks</h6>
                            <hr class="mt-1">
                        </div>
                        @include('pages.clientReport.partials.report-input', ['name' => 'backlinks_count', 'label' => 'Backlinks Count', 'type' => 'number', 'value' => $fieldValue('backlinks_count')])
                        @include('pages.clientReport.partials.report-input', ['name' => 'social_bookmark_count', 'label' => 'Social Bookmark Count', 'type' => 'number', 'value' => $fieldValue('social_bookmark_count')])
                        @include('pages.clientReport.partials.report-textarea', ['name' => 'blog_backlinks', 'label' => 'Blog Backlinks', 'value' => $textareaValue('blog_backlinks')])
                        @include('pages.clientReport.partials.report-textarea', ['name' => 'bookmark_backlinks', 'label' => 'Bookmark Backlinks', 'value' => $textareaValue('bookmark_backlinks')])

                        <div class="col-12 mt-2">
                            <h6 class="text-muted fw-semibold text-uppercase mb-0 report-section-title">Pages &amp; Performance</h6>
                            <hr class="mt-1">
                        </div>
                        @include('pages.clientReport.partials.report-input', ['name' => 'landing_page_count', 'label' => 'Landing Pages Count', 'type' => 'number', 'value' => $fieldValue('landing_page_count')])
                        @include('pages.clientReport.partials.report-textarea', ['name' => 'landing_page_urls', 'label' => 'Landing Page URLs', 'value' => $textareaValue('landing_page_urls')])
                        @foreach ([
                            'avg_pages_position' => 'Avg Pages Position',
                            'total_impressions' => 'Total Impressions',
                            'total_clicks' => 'Total Clicks',
                            'avg_ctr' => 'Avg CTR (%)',
                            'experience_score' => 'Experience Score',
                            'expertise_score' => 'Expertise Score',
                            'authority_score' => 'Authority Score',
                            'trust_score' => 'Trust Score',
                            'internal_links_count' => 'Internal Links Count',
                            'lcp' => 'LCP (s)',
                            'inp' => 'INP (ms)',
                            'cls' => 'CLS',
                            'fcp' => 'FCP (s)',
                            'ttfb' => 'TTFB (ms)',
                            'page_speed' => 'Page Speed Score',
                            'social_media_shares' => 'Social Media Shares',
                        ] as $name => $label)
                            @include('pages.clientReport.partials.report-input', ['name' => $name, 'label' => $label, 'type' => 'number', 'value' => $fieldValue($name)])
                        @endforeach
                    @else
                        <div class="col-12">
                            <h6 class="text-muted fw-semibold text-uppercase mb-0 report-section-title">SEO &amp; Keywords</h6>
                            <hr class="mt-1">
                        </div>
                        @foreach ([
                            'keywordCount' => 'Keywords Count',
                            'avg_pages_position' => 'Avg Pages Position',
                        ] as $name => $label)
                            @include('pages.clientReport.partials.report-input', ['name' => $name, 'label' => $label, 'type' => 'number', 'value' => $fieldValue($name)])
                        @endforeach
                        @include('pages.clientReport.partials.report-textarea', ['name' => 'keywordFirstpage', 'label' => 'First Page Keywords', 'value' => $textareaValue('keywordFirstpage')])
                        @include('pages.clientReport.partials.report-textarea', ['name' => 'keywordSecondpage', 'label' => 'Second Page Keywords', 'value' => $textareaValue('keywordSecondpage')])

                        <div class="col-12 mt-2">
                            <h6 class="text-muted fw-semibold text-uppercase mb-0 report-section-title">On-Page &amp; Technical SEO</h6>
                            <hr class="mt-1">
                        </div>
                        @foreach ([
                            'titleOptimized' => 'Title Optimized (%)',
                            'headingTags' => 'H1, H2, H3 Tags (%)',
                            'metaDescription' => 'Meta Description (%)',
                            'seoMetaTags' => 'SEO Meta Tags (%)',
                            'optimizedurl' => 'Optimized URL (%)',
                            'imageAltTags' => 'Image Alt Tags (%)',
                            'indexOptimization' => 'Index Optimization (%)',
                            'improveWebsiteSpeed' => 'Improve Website Speed (%)',
                        ] as $name => $label)
                            @include('pages.clientReport.partials.report-input', ['name' => $name, 'label' => $label, 'type' => 'number', 'value' => $fieldValue($name), 'max' => 100])
                        @endforeach
                        @foreach ([
                            'googleSearchConsole' => 'Google Search Console',
                            'schemaMarkup' => 'Schema Markup',
                            'robotTxt' => 'Robots.txt',
                        ] as $name => $label)
                            <div class="col-md-4">
                                <div class="form-check form-switch mt-2">
                                    <input type="hidden" name="{{ $name }}" value="0">
                                    <input class="form-check-input" type="checkbox" id="{{ $name }}" name="{{ $name }}" value="1" @checked((bool) $fieldValue($name))>
                                    <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
                                </div>
                            </div>
                        @endforeach
                        @include('pages.clientReport.partials.report-input', ['name' => 'xmlSitemap', 'label' => 'XML Sitemap Status', 'type' => 'text', 'value' => $fieldValue('xmlSitemap')])
                        @include('pages.clientReport.partials.report-input', ['name' => 'loadingSpeed', 'label' => 'Loading Speed Score', 'type' => 'text', 'value' => $fieldValue('loadingSpeed')])

                        <div class="col-12 mt-2">
                            <h6 class="text-muted fw-semibold text-uppercase mb-0 report-section-title">Pages, Backlinks &amp; Performance</h6>
                            <hr class="mt-1">
                        </div>
                        @foreach ([
                            'websiteUrlsCount' => 'Website URLs Count',
                            'internalLinks' => 'Internal Links',
                            'backlinksCount' => 'Backlinks Count',
                            'socialBookmarking' => 'Social Bookmarking',
                            'socialMediaSharing' => 'Social Media Sharing',
                            'total_impressions' => 'Total Impressions',
                            'total_clicks' => 'Total Clicks',
                            'avg_ctr' => 'Avg CTR (%)',
                            'experience_score' => 'Experience Score',
                            'expertise_score' => 'Expertise Score',
                            'authority_score' => 'Authority Score',
                            'trust_score' => 'Trust Score',
                            'lcp' => 'LCP (s)',
                            'inp' => 'INP (ms)',
                            'cls' => 'CLS',
                            'fcp' => 'FCP (s)',
                            'ttfb' => 'TTFB (ms)',
                            'page_speed' => 'Page Speed Score',
                        ] as $name => $label)
                            @include('pages.clientReport.partials.report-input', ['name' => $name, 'label' => $label, 'type' => 'number', 'value' => $fieldValue($name)])
                        @endforeach
                        @include('pages.clientReport.partials.report-textarea', ['name' => 'websiteUrls', 'label' => 'Website URLs', 'value' => $textareaValue('websiteUrls')])
                        @include('pages.clientReport.partials.report-textarea', ['name' => 'blogBacklinks', 'label' => 'Blog Backlinks', 'value' => $textareaValue('blogBacklinks')])
                        @include('pages.clientReport.partials.report-textarea', ['name' => 'bookmark_backlinks', 'label' => 'Bookmark Backlinks', 'value' => $textareaValue('bookmark_backlinks')])
                    @endif
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('reportShow', $report->uuid) }}" target="_blank" class="btn btn-outline-primary">Preview</a>
                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-content-save-outline me-1"></i>Update Report
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script src="https://firmtechservices.com/ckeditor/ckeditor.js"></script>
<script>
    $(document).ready(function () {
        if (typeof CKEDITOR !== 'undefined') {
            $('.ckeditor').each(function () {
                CKEDITOR.replace(this.id);
            });
        }

        $('#report_edit').on('submit', function () {
            if (typeof CKEDITOR !== 'undefined') {
                for (var instanceName in CKEDITOR.instances) {
                    CKEDITOR.instances[instanceName].updateElement();
                }
            }
        });
    });
</script>
@endsection
