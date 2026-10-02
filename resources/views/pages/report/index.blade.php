@extends('layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/profile-enhance.css') }}" />
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y profile-page">
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Reports /</span> Sale Report</h4>

            <!-- Range filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('salereport.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Time Range</label>
                            <select name="range" id="report_range_select" class="form-select">
                                <option value="all" {{ $rangeKey == 'all' ? 'selected' : '' }}>All Time</option>
                                <option value="this_month" {{ $rangeKey == 'this_month' ? 'selected' : '' }}>This Month</option>
                                <option value="last_month" {{ $rangeKey == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                <option value="custom" {{ $rangeKey == 'custom' ? 'selected' : '' }}>Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3 report-custom-range-field" {{ $rangeKey == 'custom' ? '' : 'style=display:none' }}>
                            <label class="form-label">From</label>
                            <input type="date" name="from" class="form-control" value="{{ $customFrom }}">
                        </div>
                        <div class="col-md-3 report-custom-range-field" {{ $rangeKey == 'custom' ? '' : 'style=display:none' }}>
                            <label class="form-label">To</label>
                            <input type="date" name="to" class="form-control" value="{{ $customTo }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Apply</button>
                        </div>
                    </form>
                    <div class="text-muted small mt-2">Showing: {{ $range['label'] }}</div>
                </div>
            </div>

            <!-- KPI tiles -->
            <div class="row g-4 mb-4">
                @php
                    $cards = [
                        ['icon' => 'mdi-currency-usd', 'label' => 'Net Revenue', 'value' => '$' . number_format($kpis['net_revenue'], 2), 'sub' => 'Revenue minus chargebacks', 'color' => 'bg-label-success'],
                        ['icon' => 'mdi-handshake-outline', 'label' => 'Total Sales', 'value' => $kpis['total_sales'], 'color' => 'bg-label-primary'],
                        ['icon' => 'mdi-laptop', 'label' => 'Development Revenue', 'value' => '$' . number_format($kpis['development_revenue'], 2), 'color' => 'bg-label-info'],
                        ['icon' => 'mdi-bullhorn-outline', 'label' => 'Marketing Revenue', 'value' => '$' . number_format($kpis['marketing_revenue'], 2), 'color' => 'bg-label-warning'],
                        ['icon' => 'mdi-cash-refund', 'label' => 'Chargebacks', 'value' => '$' . number_format($kpis['chargeback_amount'], 2), 'color' => 'bg-label-danger'],
                    ];
                @endphp
                @foreach($cards as $card)
                    @include('pages.profile.partials.stat-card', $card)
                @endforeach
            </div>

            <!-- Invoices -->
            <div class="card mb-4">
                <h5 class="card-header">Sales / Invoices</h5>
                <div class="card-datatable table-responsive">
                    @php
                        $agentOptions = $invoices->pluck('agent')->filter()->unique()->sort()->values();
                        $closerOptions = $invoices->flatMap(fn($r) => collect($r['closers'])->pluck('name'))->filter()->unique()->sort()->values();
                        $typeOptions = $invoices->flatMap(fn($r) => $r['types'])->filter()->unique()->sort()->values();
                    @endphp
                    <table id="salesReportTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr#</th>
                                <th>Date</th>
                                <th>Agent</th>
                                <th>Closer(s)</th>
                                <th>Service Type(s)</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                            <tr class="report-column-filters">
                                <th></th>
                                <th></th>
                                <th>
                                    <select class="form-select form-select-sm column-filter select2" data-column="2">
                                        <option value="">All Agents</option>
                                        @foreach($agentOptions as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select class="form-select form-select-sm column-filter select2" data-column="3">
                                        <option value="">All Closers</option>
                                        @foreach($closerOptions as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select class="form-select form-select-sm column-filter select2" data-column="4">
                                        <option value="">All Services</option>
                                        @foreach($typeOptions as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $row)
                                <tr>
                                    <td>{{ $row['sr_no'] }}</td>
                                    <td>{{ $row['date'] }}</td>
                                    <td>{{ $row['agent'] }}</td>
                                    <td>
                                        @forelse($row['closers'] as $closer)
                                            {{ $closer['name'] }}@if(!$loop->last), @endif
                                        @empty
                                            N/A
                                        @endforelse
                                    </td>
                                    <td>
                                        @foreach($row['types'] as $type)
                                            <span class="badge bg-label-secondary me-1">{{ $type }}</span>
                                        @endforeach
                                    </td>
                                    <td>${{ number_format($row['amount'], 2) }}</td>
                                    <td>
                                        @if($row['agent_id'] || $row['closers']->isNotEmpty())
                                            <div class="d-inline-block text-nowrap">
                                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-20px"></i></button>
                                                <div class="dropdown-menu dropdown-menu-end m-0">
                                                    @if($row['agent_id'])
                                                        <a href="{{ route('profile.show', $row['agent_id']) }}" class="dropdown-item"><i
                                                            class="mdi mdi-account-outline me-2"></i><span>View {{ $row['agent'] }}</span></a>
                                                    @endif
                                                    @foreach($row['closers'] as $closer)
                                                        @if($closer['id'])
                                                            <a href="{{ route('profile.show', $closer['id']) }}" class="dropdown-item"><i
                                                                class="mdi mdi-account-outline me-2"></i><span>View {{ $closer['name'] }}</span></a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Revenue trend -->
            <div class="card mb-4">
                <h5 class="card-header">Revenue Trend</h5>
                <div class="card-body">
                    @if(count($trend['labels']))
                        <div id="revenueTrendChart"></div>
                    @else
                        <div class="text-muted">No revenue in this range yet.</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="content-backdrop fade"></div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/tables-datatables-advanced.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script>
        var salesReportTable = $('#salesReportTable').DataTable({
            orderCellsTop: true,
            language: window.rsEmptyStateHTML ? { emptyTable: rsEmptyStateHTML('invoices') } : undefined
        });

        // The page-wide .select2 auto-init (form-layouts.js) registers via
        // $(document).ready(), which fires AFTER this inline script block
        // has already run — so reinitializing here directly gets silently
        // overwritten a moment later by that auto-init (dropdownParent
        // reverts to the default, and the dropdown renders inline instead
        // of escaping to <body>, getting visually buried under the table
        // rows below it). Registering this in its own ready handler queues
        // it to run strictly after the auto-init's, guaranteeing this
        // config wins.
        $(function () {
            $('.report-column-filters .select2').each(function () {
                var $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
                $el.select2({
                    width: '100%',
                    dropdownParent: $('body')
                });
            });
        });

        $('.report-column-filters .column-filter').on('change', function () {
            var columnIndex = $(this).data('column');
            salesReportTable.column(columnIndex).search(this.value).draw();
        });

        // The filter row's <select> clicks shouldn't trigger the column
        // sort click handler bound to the header row above it.
        $('.report-column-filters th').on('click', function (e) {
            e.stopPropagation();
        });

        document.getElementById('report_range_select').addEventListener('change', function () {
            var isCustom = this.value === 'custom';
            document.querySelectorAll('.report-custom-range-field').forEach(function (el) {
                el.style.display = isCustom ? '' : 'none';
            });
        });

        var trendEl = document.querySelector('#revenueTrendChart');
        if (trendEl) {
            var labelColor = (typeof isDarkStyle !== 'undefined' && isDarkStyle) ? config.colors_dark.textMuted : config.colors.textMuted;

            new ApexCharts(trendEl, {
                chart: {
                    height: 350,
                    type: 'area',
                    toolbar: { show: false },
                    parentHeightOffset: 0
                },
                series: [
                    { name: 'Development', data: @json($trend['development']) },
                    { name: 'Marketing', data: @json($trend['marketing']) }
                ],
                xaxis: {
                    categories: @json($trend['labels']),
                    labels: { style: { colors: labelColor } }
                },
                yaxis: {
                    labels: {
                        style: { colors: labelColor },
                        formatter: function (val) { return '$' + Math.round(val).toLocaleString(); }
                    }
                },
                colors: [config.colors.info, config.colors.warning],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 }
                },
                legend: { position: 'top' },
                tooltip: {
                    y: { formatter: function (val) { return '$' + Number(val).toLocaleString(); } }
                }
            }).render();
        }
    </script>
@endsection
