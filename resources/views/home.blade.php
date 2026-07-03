@auth
@php
$user = Auth::user();
@endphp
@endauth
@extends('layouts.dashboard')
@section('styles')

@endsection
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gy-4">
        <!-- Gamification Card -->
        <div class="col-md-6 col-lg-6">
            <div class="card h-100">
                <div class="d-flex align-items-end row">
                    <div class="col-md-6 order-2 order-md-1">
                        <div class="card-body">
                            <h4 class="card-title pb-xl-2">Welcome {{$user->name}}!🎉</h4>
                            <p>Check your new badge in your profile.</p>
                            <a id="startBreakBtn" href="javascript:;" class="btn btn-primary">Start Break</a>
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
        <div class="col-lg-3 col-sm-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="mdi mdi-cart-plus mdi-24px"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            {{-- <p class="mb-0 text-success me-1">+22%</p> --}}
                            {{-- <i class="mdi mdi-chevron-up text-success"></i> --}}
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
        </div>
        <!--/ Statistics Total Order -->

        <!-- Sessions line chart -->
        <div class="col-lg-3 col-sm-6">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-end mb-1 flex-wrap gap-2">
                        <h4 class="mb-0 me-2">${{ $total }}</h4>
                    </div>
                    <span class="d-block mb-2 text-body">Revenue </span>
                </div>
                <div class="card-body pt-0">
                    <div id="sessions"></div>
                </div>
            </div>
        </div>
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



        <!-- Multiple widgets -->
        <div class="col-md-6 col-xl-4">
            <div class="row g-4">
                <div class="col-xl-12">
          <div class="card h-100">
            <div class="card-body d-flex justify-content-between">
              <div class="d-flex flex-column">
                <div class="card-title mb-auto">
                  <h5 class="mb-0 text-nowrap">Total Lates</h5>
                  <p class="mb-0">This Month</p>
                </div>
                <div class="chart-statistics">
                  <h3 class="card-title mb-0">{{ $lates }} Lates</h3>
                  {{-- <p class="text-success text-nowrap mb-0"><i class="icon-base ti tabler-chevron-up me-1"></i> 15.8%</p> --}}
                </div>
              </div>
              <div id="generatedLeadsChart" style="min-height: 132px;" class=""><div id="apexchartsc81ehgxzg" class="apexcharts-canvas apexchartsc81ehgxzg apexcharts-theme-" style="width: 120px; height: 132px;"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" width="120" height="132"><foreignObject x="0" y="0" width="120" height="132"><div class="apexcharts-legend" xmlns="http://www.w3.org/1999/xhtml"></div>
                </foreignObject><g class="apexcharts-inner apexcharts-graphical" transform="translate(-20, 15)"><defs><clipPath id="gridRectMaskc81ehgxzg"><rect width="160" height="110" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="gridRectBarMaskc81ehgxzg"><rect width="164" height="114" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="gridRectMarkerMaskc81ehgxzg"><rect width="160" height="110" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="forecastMaskc81ehgxzg"></clipPath><clipPath id="nonForecastMaskc81ehgxzg"></clipPath></defs><g class="apexcharts-pie"><g transform="translate(0, 0) scale(1)"><circle r="34.7609756097561" cx="80" cy="55" fill="transparent"></circle><g class="apexcharts-slices"><g class="apexcharts-series apexcharts-pie-series" seriesName="Electronic" rel="1" data:realIndex="0"><path d="M 80 5.341463414634141 A 49.65853658536586 49.65853658536586 0 0 1 129.64207316729613 53.72139628776691 L 114.74945121710729 54.104977401436834 A 34.7609756097561 34.7609756097561 0 0 0 80 20.239024390243898 L 80 5.341463414634141 z " fill="rgba(36,179,100,1)" fill-opacity="1" stroke="#ffffff" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-pie-area apexcharts-donut-slice-0" index="0" j="0" data:angle="88.52459016393442" data:startAngle="0" data:strokeWidth="0" data:value="45" data:pathOrig="M 80 5.341463414634141 A 49.65853658536586 49.65853658536586 0 0 1 129.64207316729613 53.72139628776691 L 114.74945121710729 54.104977401436834 A 34.7609756097561 34.7609756097561 0 0 0 80 20.239024390243898 L 80 5.341463414634141 z "></path></g><g class="apexcharts-series apexcharts-pie-series" seriesName="Sports" rel="2" data:realIndex="1"><path d="M 129.64207316729613 53.72139628776691 A 49.65853658536586 49.65853658536586 0 0 1 60.89809407308648 100.8376204199069 L 66.62866585116053 87.08633429393483 A 34.7609756097561 34.7609756097561 0 0 0 114.74945121710729 54.104977401436834 L 129.64207316729613 53.72139628776691 z " fill="rgba(83,210,140,1)" fill-opacity="1" stroke="#ffffff" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-pie-area apexcharts-donut-slice-1" index="0" j="1" data:angle="114.09836065573771" data:startAngle="88.52459016393442" data:strokeWidth="0" data:value="58" data:pathOrig="M 129.64207316729613 53.72139628776691 A 49.65853658536586 49.65853658536586 0 0 1 60.89809407308648 100.8376204199069 L 66.62866585116053 87.08633429393483 A 34.7609756097561 34.7609756097561 0 0 0 114.74945121710729 54.104977401436834 L 129.64207316729613 53.72139628776691 z "></path></g><g class="apexcharts-series apexcharts-pie-series" seriesName="Decor" rel="3" data:realIndex="2"><path d="M 60.89809407308648 100.8376204199069 A 49.65853658536586 49.65853658536586 0 0 1 30.869213831510372 62.220533655227136 L 45.60844968205726 60.054373558658995 A 34.7609756097561 34.7609756097561 0 0 0 66.62866585116053 87.08633429393483 L 60.89809407308648 100.8376204199069 z " fill="rgba(126,221,169,1)" fill-opacity="1" stroke="#ffffff" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-pie-area apexcharts-donut-slice-2" index="0" j="2" data:angle="59.016393442622956" data:startAngle="202.62295081967213" data:strokeWidth="0" data:value="30" data:pathOrig="M 60.89809407308648 100.8376204199069 A 49.65853658536586 49.65853658536586 0 0 1 30.869213831510372 62.220533655227136 L 45.60844968205726 60.054373558658995 A 34.7609756097561 34.7609756097561 0 0 0 66.62866585116053 87.08633429393483 L 60.89809407308648 100.8376204199069 z "></path></g><g class="apexcharts-series apexcharts-pie-series" seriesName="Fashion" rel="4" data:realIndex="3"><path d="M 30.869213831510372 62.220533655227136 A 49.65853658536586 49.65853658536586 0 0 1 79.99133295039263 5.341464170976899 L 79.99393306527485 20.23902491968383 A 34.7609756097561 34.7609756097561 0 0 0 45.60844968205726 60.054373558658995 L 30.869213831510372 62.220533655227136 z " fill="rgba(169,233,197,1)" fill-opacity="1" stroke="#ffffff" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-pie-area apexcharts-donut-slice-3" index="0" j="3" data:angle="98.36065573770492" data:startAngle="261.6393442622951" data:strokeWidth="0" data:value="50" data:pathOrig="M 30.869213831510372 62.220533655227136 A 49.65853658536586 49.65853658536586 0 0 1 79.99133295039263 5.341464170976899 L 79.99393306527485 20.23902491968383 A 34.7609756097561 34.7609756097561 0 0 0 45.60844968205726 60.054373558658995 L 30.869213831510372 62.220533655227136 z "></path></g></g></g><g class="apexcharts-datalabels-group" transform="translate(0, 0) scale(1)"><text x="80" y="75" text-anchor="middle" dominant-baseline="auto" font-size=".8125rem" font-family="fontFamily" font-weight="400" fill="var(--bs-success)" class="apexcharts-text apexcharts-datalabel-label" style="font-family: fontFamily;">Total</text><text x="80" y="56" text-anchor="middle" dominant-baseline="auto" font-size="1.5rem" font-family="fontFamily" font-weight="500" fill="var(--bs-heading-color)" class="apexcharts-text apexcharts-datalabel-value" style="font-family: fontFamily;">{{ $lates }}</text></g></g><line x1="0" y1="0" x2="160" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" stroke-linecap="butt" class="apexcharts-ycrosshairs"></line><line x1="0" y1="0" x2="160" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="0" stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line></g><g class="apexcharts-datalabels-group" transform="translate(0, 0) scale(1)"></g></svg><div class="apexcharts-tooltip apexcharts-theme-false" style="left: 51.3984px; top: -12.75px;"><div class="apexcharts-tooltip-series-group apexcharts-tooltip-series-group-0 apexcharts-active" style="order: 1; display: flex; background-color: rgb(36, 179, 100);"><span class="apexcharts-tooltip-marker" style="background-color: rgb(36, 179, 100); display: none;"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label">Electronic: </span><span class="apexcharts-tooltip-text-y-value">45</span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div><div class="apexcharts-tooltip-series-group apexcharts-tooltip-series-group-1" style="order: 2; display: none; background-color: rgb(36, 179, 100);"><span class="apexcharts-tooltip-marker" style="background-color: rgb(36, 179, 100); display: none;"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label">Electronic: </span><span class="apexcharts-tooltip-text-y-value">45</span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div><div class="apexcharts-tooltip-series-group apexcharts-tooltip-series-group-2" style="order: 3; display: none; background-color: rgb(36, 179, 100);"><span class="apexcharts-tooltip-marker" style="background-color: rgb(36, 179, 100); display: none;"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label">Electronic: </span><span class="apexcharts-tooltip-text-y-value">45</span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div><div class="apexcharts-tooltip-series-group apexcharts-tooltip-series-group-3" style="order: 4; display: none; background-color: rgb(36, 179, 100);"><span class="apexcharts-tooltip-marker" style="background-color: rgb(36, 179, 100); display: none;"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label">Electronic: </span><span class="apexcharts-tooltip-text-y-value">45</span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div></div></div>
            </div>
          </div>
        </div>
                <!-- Total Revenue chart -->
                {{-- <div class="col-md-6 col-sm-6">
                    <div class="card h-100">
                        <div class="card-header pb-0">
                            <div class="d-flex align-items-end mb-1 flex-wrap gap-2">
                                <h4 class="mb-0 me-2">$42.5k</h4>
                                <p class="mb-0 text-danger">-22%</p>
                            </div>
                            <span class="d-block mb-2 text-body">Total Revenue</span>
                        </div>
                        <div class="card-body">
                            <div id="totalRevenue"></div>
                        </div>
                    </div>
                </div> --}}
                <!--/ Total Revenue chart -->

                {{-- <div class="col-md-6 col-sm-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="mdi mdi-currency-usd mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <p class="mb-0 text-success me-1">+38%</p>
                                    <i class="mdi mdi-chevron-up text-success"></i>
                                </div>
                            </div>
                            <div class="card-info mt-4 pt-3">
                                <h5 class="mb-2">$13.4k</h5>
                                <p class="text-body">Total Sales</p>
                                <div class="badge bg-label-secondary rounded-pill mt-1">Last Six Month</div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                {{-- <div class="col-md-6 col-sm-6">
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
                </div> --}}
                <!-- overview Radial chart -->
                {{-- <div class="col-md-6 col-sm-6">
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
                </div> --}}
                <!--/ overview Radial chart -->
            </div>
        </div>
        <!--/ Multiple widgets -->
        @if ($user->role->name == 'Creator' || $user->role->name == 'Executives' || $user->role->name == 'Closer' || $user->role->name == 'QA')
            <!-- Project Statistics -->
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title m-0 me-2">Today's Break Duration</h5>
                            </div>
                            <div class="d-flex justify-content-between py-2 px-4 border-bottom">
                                <h6 class="mb-0 small">NAME</h6>
                                <h6 class="mb-0 small">Duration</h6>
                            </div>
                            <div class="card-body">
                                <ul class="p-0 m-0">
                                    @foreach ($users as $b_user)
                                        <li class="d-flex mb-4">
                                            <div class="avatar avatar-md flex-shrink-0 me-3">
                                                <div class="avatar-initial bg-lighter rounded">
                                                    <div>
                                                        <img src="{{ asset('assets/img/avatars/5.png') }}" alt="User"
                                                            class="h-25" style="border-radius: 10%" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0">{{ $b_user->name }}</h6>
                                                    <small>{{ $b_user->role->name }}</small>
                                                </div>
                                                <div class="badge bg-label-primary rounded-pill">
                                                    @php
                                                        $todayBreaks = $b_user->attendances
                                                            ->where('shift_date', $shiftDate)
                                                            ->flatMap->breaks;
                                                    @endphp
                                                    {{ gmdate('H:i:s', $todayBreaks->sum('duration')) }}
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                    </div>
            <!--/ Project Statistics -->
        @endif

        @if ($user->role->name == 'Creator' || $user->role->name == 'Executives' || $user->role->name == 'Closer' || $user->role->name == 'QA')
            <!-- Sales Country Chart -->
            <div class="col-12 col-xl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-1">Service Leads</h5>
                        </div>
                        <p class="mb-0 text-body">Total {{ $services->sum('leads_count') }} Leads</p>
                    </div>
                    <div class="card-body pb-1 px-0">
                        <div id="salescategoryChart"></div>
                    </div>
                </div>
            </div>
            <!--/ Sales Country Chart -->
        @endif





    </div>
</div>
<div class="modal fade" id="breakModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-white text-white d-flex align-items-center justify-content-center">

            <div class="text-center">
                <img src="{{asset('assets/img/coffee-break-pana.svg')}}" style="width: 100%; height: 400px;">
                <h1 style="padding-top: 20px; color: #636578" class="mb-4">{{$user->name}} is On Break</h1>

                <h2 id="breakTimer" style="font-size: 60px; color: #666cff">
                    00:00:00
                </h2>

                <button class="btn  btn-danger mt-5 px-5 py-3" id="endBreakBtn">
                    End Break
                </button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="breakModaltype" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-white text-white d-flex align-items-center justify-content-center">

            <div class="text-center">
                <img src="{{asset('assets/img/coffee-break-pana.svg')}}" style="width: 100%; height: 400px;">
                <h1 style="padding-top: 20px; color: #636578" class="mb-4">Select Break Type</h1>
                <div class="d-flex justify-content-center gap-4">
                    <input type="radio" class="btn-check" name="breakType" id="mealBreak" autocomplete="off" checked>
                    <label class="btn  btn-primary mt-5 px-5 py-3" for="mealBreak">
                        Meal Break
                    </label>
                    <input type="radio" class="btn-check" name="breakType" id="2ndBreak" autocomplete="off">
                    <label class="btn  btn-primary mt-5 px-5 py-3" for="2ndBreak">
                        2nd Break
                    </label>
                    <input type="radio" class="btn-check" name="breakType" id="smokeBreak" autocomplete="off">
                    <label class="btn  btn-primary mt-5 px-5 py-3" for="smokeBreak">
                        Smoke Break
                    </label>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let timerInterval;
    let seconds = 0;
    let isOnBreak = false;

    // Init Bootstrap Modal
    const breakModalEl = document.getElementById('breakModal');
    const breakModalE2 = document.getElementById('breakModaltype');
    const breakModal = new bootstrap.Modal(breakModalEl, {
        backdrop: 'static',
        keyboard: false
    });
    const breakModaltype = new bootstrap.Modal(breakModalE2, {
        backdrop: 'static',
        keyboard: false
    });

    // Format Timer
    function formatTime(sec) {
        let h = String(Math.floor(sec / 3600)).padStart(2, '0');
        let m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
        let s = String(sec % 60).padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    // Start Timer
    function startTimer() {
        timerInterval = setInterval(() => {
            seconds++;
            document.getElementById('breakTimer').innerText = formatTime(seconds);
        }, 1000);
    }

    // Stop Timer
    function stopTimer() {
        clearInterval(timerInterval);
        seconds = 0;
    }

    // Fullscreen Mode
    function openFullscreen() {
        let elem = document.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen().catch(() => {});
        }
    }

    function exitFullscreen() {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        }
    }

        // START BREAK
        document.getElementById('startBreakBtn').addEventListener('click', function () {
            // 1. Show the break type selection modal first
            breakModaltype.show();
            openFullscreen();

            // 2. Listen for break type selection
            document.querySelectorAll('input[name="breakType"]').forEach((elem) => {
                elem.addEventListener('click', function () {
                    const val = document.querySelector('input[name="breakType"]:checked').id;
                    console.log('Selected break type:', val);
                    // 3. Hide the type selection modal
                    breakModaltype.hide();
                    // 4. NOW send the fetch with the selected break type
                    fetch('{{ route('front.stratBreak') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'   // changed
                        },
                            body: JSON.stringify({ break_type: val })
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data);
                        // 5. Only start the break UI after successful response
                        isOnBreak = true;
                        seconds = 0;
                        breakModal.show();
                        openFullscreen();
                        startTimer();
                    })
                    .catch(error => {
                        alert('Error starting break. Please try again.');
                        console.error(error);
                    });
                });
            });
        });

        // END BREAK
        document.getElementById('endBreakBtn').addEventListener('click', function () {

            if (!isOnBreak) return;

            fetch('{{ route('front.endBreak') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            isOnBreak = false;

            stopTimer();
            breakModal.hide();
            exitFullscreen();
            location.reload();
        });

        // Prevent leaving tab (basic)
        document.addEventListener('visibilitychange', function () {
            if (isOnBreak && document.hidden) {
                alert('⚠️ You are on break. Please stay on this screen.');
                {{-- location.reload(); --}}
                // strict handling
            }
        });

        // Prevent ESC key manually (extra safety)
        document.addEventListener('keydown', function (e) {
            if (isOnBreak && e.key === "Escape") {
                e.preventDefault();
            }
        });

        // Prevent right click (optional strict)
        document.addEventListener('contextmenu', function (e) {
            if (isOnBreak) {
                e.preventDefault();
            }
        });

        // Force focus back (optional strict)
        window.onblur = function () {
            if (isOnBreak) {
                setTimeout(() => {
                    window.focus();
                }, 100);
            }
        };

});
</script>
{{-- <script>
    const services = @json($services);
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

      const salesCountryChartEl = document.querySelector('#salesCountryChart'),
    salesCountryChartConfig = {
      chart: {
        type: 'bar',
        height: 368,
        parentHeightOffset: 0,
        toolbar: {
          show: false
        }
      },
      series: [
        {
          name: 'Sales',
          data: [20, 40, 12375, 76, 89]
        }
      ],
      plotOptions: {
        bar: {
          borderRadius: 10,
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
        categories: ['SEO', 'LandingPage', 'GMB', 'Website Development', 'SMM'],
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
  if (typeof salesCountryChartEl !== undefined && salesCountryChartEl !== null) {
    const salesCountryChart = new ApexCharts(salesCountryChartEl, salesCountryChartConfig);
    salesCountryChart.render();
  }
</script> --}}

<script>
const services = @json($services);

const categories = services.map(item => item.name);
const data = services.map(item => item.leads_count);

const salesCountryChartEl = document.querySelector('#salescategoryChart');
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
const salesCountryChartConfig = {
  chart: {
    type: 'bar',
    height: 368,
    toolbar: { show: false }
  },

  series: [
    {
      name: 'Leads',
      data: data
    }
  ],

  plotOptions: {
    bar: {
      horizontal: true,
      distributed: true,
      borderRadius: 10,
      barHeight: '60%'
    }
  },

  dataLabels: {
    enabled: true
  },

  colors: [
    config.colors.primary,
    config.colors.success,
    config.colors.warning,
    config.colors.info,
    config.colors.danger
  ],
    // borderColor = config.colors.borderColor;
  xaxis: {
    categories: categories
  },

  grid: {
    borderColor: borderColor
  }
};

if (salesCountryChartEl) {
  new ApexCharts(salesCountryChartEl, salesCountryChartConfig).render();
}
</script>
@endsection
