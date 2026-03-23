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
        <div class="col-md-6 col-lg-6">
            <div class="card h-100">
                <div class="d-flex align-items-end row">
                    <div class="col-md-6 order-2 order-md-1">
                        <div class="card-body">
                            <h4 class="card-title pb-xl-2">Welcome {{$user->name}}!🎉</h4>
                            {{-- <p class="mb-0">You have done <span class="h6 mb-0">68%</span>😎 more sales today.</p>
                            --}}
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
                        {{-- <p class="mb-0 text-success">+62%</p> --}}
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

        <!-- Project Statistics -->
        {{-- <div class="col-md-6 col-xl-4">
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
        </div> --}}
        <!--/ Project Statistics -->

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

        <!-- Sales Country Chart -->
        {{-- <div class="col-12 col-xl-4 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-1">Sales Country</h5>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="salesCountryDropdown" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-dots-vertical mdi-24px"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesCountryDropdown">
                                <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                            </div>
                        </div>
                    </div>
                    <p class="mb-0 text-body">Total $42,580 Sales</p>
                </div>
                <div class="card-body pb-1 px-0">
                    <div id="salesCountryChart"></div>
                </div>
            </div>
        </div> --}}
        <!--/ Sales Country Chart -->






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
@endsection
@section('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let timerInterval;
    let seconds = 0;
    let isOnBreak = false;
    


    // Init Bootstrap Modal
    const breakModalEl = document.getElementById('breakModal');
    const breakModal = new bootstrap.Modal(breakModalEl, {
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

        if (isOnBreak) return;

        fetch('{{ route('front.stratBreak') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        isOnBreak = true;
        seconds = 0;

        breakModal.show();
        openFullscreen();
        startTimer();
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
@endsection