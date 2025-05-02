@auth
@php
$user = Auth::user();
@endphp
@endauth
@extends('layouts.dashboard')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gy-4">
        <!-- Gamification Card -->
        <div class="col-md-12 col-lg-12">
            <div class="card h-100">
                <div class="d-flex align-items-end row">
                    <div class="col-md-6 order-2 order-md-1">
                        <div class="card-body">
                            <h4 class="card-title pb-xl-2">Welcome {{$user->name}}!🎉</h4>
                            {{-- <p class="mb-0">You have done <span class="h6 mb-0">68%</span>😎 more sales today.</p>
                            --}}
                            <p>Check your new badge in your profile.</p>
                            <a href="javascript:;" class="btn btn-primary">View Profile</a>
                        </div>
                    </div>
                    <div class="col-md-6 text-center text-md-end order-1 order-md-2">
                        <div class="card-body pb-0 px-0 px-md-4 ps-0">
                            <img src="../../assets/img/illustrations/illustration-john-light.png" height="180"
                                alt="View Profile" data-app-light-img="illustrations/illustration-john-light.png"
                                data-app-dark-img="illustrations/illustration-john-dark.png" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Gamification Card -->

        <!-- Statistics Total Order -->
        {{-- <div class="col-lg-2 col-sm-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="mdi mdi-cart-plus mdi-24px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4 pt-1 mt-lg-1 mt-xl-4">
                        @if(isset($sale_count))
                        <h5 class="mb-2">{{ $sale_count }}</h5>
                        @else
                        <h5 class="mb-2">0</h5>
                        @endif
                        <p class="mb-lg-2 mb-xl-3">Total Sales</p>
                        <div class="badge bg-label-secondary rounded-pill">Current Month</div>
                    </div>
                </div>
            </div>
        </div> --}}
        <!--/ Statistics Total Order -->

        <!-- Sessions line chart -->
        {{-- <div class="col-lg-2 col-sm-6">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-end mb-1 flex-wrap gap-2">
                        <h4 class="mb-0 me-2">${{ $total }}</h4>
                        <p class="mb-0 text-success">+62%</p>
                    </div>
                    <span class="d-block mb-2 text-body">Revenue </span>
                </div>
                <div class="card-body pt-0">
                    <div id="sessions"></div>
                </div>
            </div>
        </div> --}}
        <!--/ Sessions line chart -->

        <!-- Total Transactions & Report Chart -->
        {{-- <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="row">
                    <div class="col-md-7 col-12 order-2 order-md-0">
                        <div class="card-header">
                            <h5 class="mb-0">Total Transactions</h5>
                        </div>
                        <div class="card-body">
                            <div id="totalTransactionChart"></div>
                        </div>
                    </div>
                    <div class="col-md-5 col-12 border-start">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h5 class="mb-1">Report</h5>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="totalTransaction"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical mdi-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalTransaction">
                                        <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Share</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Update</a>
                                    </div>
                                </div>
                            </div>
                            <p class="mb-0 text-body">Last month transactions $234.40k</p>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row">
                                <div class="col-6 border-end">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-success rounded">
                                                <div class="mdi mdi-trending-up mdi-24px"></div>
                                            </div>
                                        </div>
                                        <p class="my-2">This Week</p>
                                        <h6 class="mb-0">+82.45%</h6>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-primary rounded">
                                                <div class="mdi mdi-trending-down mdi-24px"></div>
                                            </div>
                                        </div>
                                        <p class="my-2">This Week</p>
                                        <h6 class="mb-0">-24.86%</h6>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4" />
                            <div class="d-flex justify-content-around flex-wrap gap-2">
                                <div>
                                    <p class="mb-1">Performance</p>
                                    <h6 class="mb-0">+94.15%</h6>
                                </div>
                                <div>
                                    <button class="btn btn-primary" type="button">view report</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        <!--/ Total Transactions & Report Chart -->

        <!-- Performance Chart -->
        {{-- <div class="col-12 col-xl-4 col-md-6">
            <div class="card h-100">
                <div class="card-header pb-1">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-1">Performance</h5>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="performanceDropdown" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-dots-vertical mdi-24px"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="performanceDropdown">
                                <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-0 pt-1">
                    <div id="performanceChart"></div>
                </div>
            </div>
        </div> --}}
        <!--/ Performance Chart -->

        <!-- Project Statistics -->
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Project Statistics</h5>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="projectStatus" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical mdi-24px"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="projectStatus">
                            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between py-2 px-4 border-bottom">
                    <h6 class="mb-0 small">NAME</h6>
                    <h6 class="mb-0 small">BUDGET</h6>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        <li class="d-flex mb-4">
                            <div class="avatar avatar-md flex-shrink-0 me-3">
                                <div class="avatar-initial bg-lighter rounded">
                                    <div>
                                        <img src="../../assets/img/icons/misc/3d-illustration.png" alt="User"
                                            class="h-25" />
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">3D Illustration</h6>
                                    <small>Blender Illustration</small>
                                </div>
                                <div class="badge bg-label-primary rounded-pill">$6,500</div>
                            </div>
                        </li>
                        <li class="d-flex mb-4">
                            <div class="avatar avatar-md flex-shrink-0 me-3">
                                <div class="avatar-initial bg-lighter rounded">
                                    <div>
                                        <img src="../../assets/img/icons/misc/finance-app-design.png" alt="User"
                                            class="h-25" />
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Finance App Design</h6>
                                    <small>Figma UI Kit</small>
                                </div>
                                <div class="badge bg-label-primary rounded-pill">$4,290</div>
                            </div>
                        </li>
                        <li class="d-flex mb-4">
                            <div class="avatar avatar-md flex-shrink-0 me-3">
                                <div class="avatar-initial bg-lighter rounded">
                                    <div>
                                        <img src="../../assets/img/icons/misc/4-square.png" alt="User" class="h-25" />
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">4 Square</h6>
                                    <small>Android Application</small>
                                </div>
                                <div class="badge bg-label-primary rounded-pill">$44,500</div>
                            </div>
                        </li>
                        <li class="d-flex mb-4">
                            <div class="avatar avatar-md flex-shrink-0 me-3">
                                <div class="avatar-initial bg-lighter rounded">
                                    <div>
                                        <img src="../../assets/img/icons/misc/delta-web-app.png" alt="User"
                                            class="h-25" />
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Delta Web App</h6>
                                    <small>React Dashboard</small>
                                </div>
                                <div class="badge bg-label-primary rounded-pill">$12,690</div>
                            </div>
                        </li>
                        <li class="d-flex">
                            <div class="avatar avatar-md flex-shrink-0 me-3">
                                <div class="avatar-initial bg-lighter rounded">
                                    <div>
                                        <img src="../../assets/img/icons/misc/ecommerce-website.png" alt="User"
                                            class="h-25" />
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">eCommerce Website</h6>
                                    <small>Vue + Laravel</small>
                                </div>
                                <div class="badge bg-label-primary rounded-pill">$10,850</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--/ Project Statistics -->

        <!-- Multiple widgets -->
        <div class="col-md-6 col-xl-4">
            <div class="row g-4">
                <!-- Total Revenue chart -->
                <div class="col-md-6 col-sm-6">
                    <div class="card h-100">
                        <div class="card-header pb-0">
                            <div class="d-flex align-items-end mb-1 flex-wrap gap-2">
                                <h4 class="mb-0 me-2">${{ $total }}</h4>
                                {{-- <p class="mb-0 text-danger">-22%</p> --}}
                            </div>
                            <span class="d-block mb-2 text-body">Total Revenue</span>
                        </div>
                        <div class="card-body">
                            <div id="totalRevenue"></div>
                        </div>
                    </div>
                </div>
                <!--/ Total Revenue chart -->

                <div class="col-md-6 col-sm-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="mdi mdi-currency-usd mdi-24px"></i>
                                    </div>
                                </div>
                                {{-- <div class="d-flex align-items-center">
                                    <p class="mb-0 text-success me-1">+38%</p>
                                    <i class="mdi mdi-chevron-up text-success"></i>
                                </div> --}}
                            </div>
                            <div class="card-info mt-4 pt-3">
                                <h5 class="mb-2">{{ $sale_count }}</h5>
                                <p class="text-body">Total Sales</p>
                                <div class="badge bg-label-secondary rounded-pill mt-1">Current Month</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <i class="mdi mdi-link mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <p class="mb-0 text-success me-1">+62%</p>
                                    <i class="mdi mdi-chevron-up text-success"></i>
                                </div>
                            </div>
                            <div class="card-info mt-4 pt-4">
                                <h5 class="mb-2">142.8k</h5>
                                <p class="text-body">Total Impression</p>
                                <div class="badge bg-label-secondary rounded-pill">Last One Year</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- overview Radial chart -->
                <div class="col-md-6 col-sm-6">
                    <div class="card h-100">
                        <div class="card-header pb-0">
                            <div class="d-flex align-items-end mb-1 flex-wrap gap-2">
                                <h4 class="mb-0 me-2">$67.1k</h4>
                                <p class="mb-0 text-success">+49%</p>
                            </div>
                            <span class="d-block mb-2 text-body">Overview</span>
                        </div>
                        <div class="card-body pt-0">
                            <div id="overviewChart" class="d-flex align-items-center"></div>
                        </div>
                    </div>
                </div>
                <!--/ overview Radial chart -->
            </div>
        </div>
        <!--/ Multiple widgets -->

        <!-- Sales Country Chart -->
        <div class="col-12 col-xl-4 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-1">Sales States</h5>
                    </div>
                    <p class="mb-0 text-body">Total {{ $totalStates }} States</p>
                </div>
                <div class="card-body pb-1 px-0">
                    <div id="salesCountryChart"></div>
                </div>
            </div>
        </div>
        <!--/ Sales Country Chart -->
    </div>
</div>
@endsection
@section('js')
<script>

let cardColor, labelColor, headingColor, borderColor, grayColor, currentTheme, bodyColorLabel;

if (isDarkStyle) {
  cardColor = config.colors_dark.cardColor;
  labelColor = config.colors_dark.textMuted;
  headingColor = config.colors_dark.headingColor;
  borderColor = config.colors_dark.borderColor;
  grayColor = '#3b3e59';
  currentTheme = 'dark';
  bodyColorLabel = config.colors_dark.bodyColor;
} else {
  cardColor = config.colors.cardColor;
  labelColor = config.colors.textMuted;
  headingColor = config.colors.headingColor;
  borderColor = config.colors.borderColor;
  grayColor = '#f4f4f6';
  currentTheme = 'light';
  bodyColorLabel = config.colors.bodyColor;
}

const chartColors = {
  donut: {
    series1: config.colors.warning,
    series2: '#fdb528cc',
    series3: '#fdb52899',
    series4: '#fdb52866',
    series5: config.colors_label.warning
  },
  donut2: {
    series1: config.colors.success,
    series2: '#43ff64e6',
    series3: '#43ff6473',
    series4: '#43ff6433'
  },
  line: {
    series1: config.colors.warning,
    series2: config.colors.primary,
    series3: '#7367f029'
  }
};
    // Pass the data from Laravel to JavaScript
    const responseData = @json($topStates);

    // Extract the state names and lead counts from the response
    const categories = responseData.map(item => item.state);
    const data = responseData.map(item => item.lead_count);

    const salesCountryChartEl = document.querySelector('#salesCountryChart');
    const salesCountryChartConfig = {
        chart: {
            type: 'bar',
            height: 295,
            parentHeightOffset: 0,
            toolbar: {
                show: false
            }
        },
        series: [
            {
                name: 'Leads',
                data: data
            }
        ],
        plotOptions: {
            bar: {
                borderRadius: 8,
                barHeight: '60%',
                horizontal: true,
                distributed: true,
                startingShape: 'rounded',
                dataLabels: {
                    position: 'bottom'
                }
            }
        },
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            offsetY: 8,
            offsetX: 11,
            style: {
                fontWeight: 500,
                fontSize: '0.9375rem',
                fontFamily: 'Inter'
            }
        },
        tooltip: {
            enabled: false
        },
        legend: {
            show: false
        },
        colors: [
            config.colors.primary,
            config.colors.success,
            config.colors.warning,
            config.colors.info,
            config.colors.danger
        ],
        grid: {
            strokeDashArray: 8,
            borderColor,
            xaxis: { lines: { show: true } },
            yaxis: { lines: { show: false } },
            padding: {
                top: -18,
                left: 21,
                right: 33,
                bottom: 10
            }
        },
        xaxis: {
            categories: categories, // Dynamically set the categories based on response data
            labels: {
                formatter: function (val) {
                    return Number(val / 1000) + 'K';
                },
                style: {
                    fontSize: '0.9375rem',
                    colors: labelColor,
                    fontFamily: 'Inter'
                }
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        },
        yaxis: {
            labels: {
                style: {
                    fontWeight: 500,
                    fontSize: '0.9375rem',
                    colors: headingColor,
                    fontFamily: 'Inter'
                }
            }
        },
        states: {
            hover: {
                filter: {
                    type: 'none'
                }
            },
            active: {
                filter: {
                    type: 'none'
                }
            }
        }
    };

    // Render the chart if the element exists
    if (typeof salesCountryChartEl !== undefined && salesCountryChartEl !== null) {
        const salesCountryChart = new ApexCharts(salesCountryChartEl, salesCountryChartConfig);
        salesCountryChart.render();
    }
</script>
@endsection
