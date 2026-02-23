@extends('layouts.dashboard')
@section('css')



@endsection
@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Charts /</span> Chart.js</h4>

            <div class="row">
              <!-- Line Charts -->

              <div class="col-12 mb-4">
                <div class="card">
                  <div class="card-header header-elements">
                    <div>
                      <h5 class="card-title mb-0">Statistics</h5>
                      <small class="text-muted">Commercial networks and enterprises</small>
                    </div>
                    <div class="card-header-elements ms-auto py-0">
                      <h5 class="mb-0 me-3">Total: ${{ number_format($total, 2) }}</h5>
                    </div>
                  </div>
                  <div class="card-body pt-2">
                    <canvas id="lineChart" class="chartjs" data-height="500"></canvas>
                  </div>
                </div>
              </div>
              <div class="col-xl-12 col-12 mb-4">
                <div class="card">
                  <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Latest Statistics</h5>
                    <div class="card-action-element ms-auto py-0">
                    </div>
                  </div>
                  <div class="card-body">
                    <canvas id="barChart" class="chartjs" data-height="400"></canvas>
                  </div>
                </div>
              </div>
              <!-- /Line Charts -->

              <!-- Radar Chart -->
              {{-- <div class="col-lg-6 col-12 mb-4">
                <div class="card">
                  <div class="card-header">
                    <h5 class="card-title mb-0">Radar Chart</h5>
                  </div>
                  <div class="card-body pt-2">
                    <canvas class="chartjs" id="radarChart" data-height="355"></canvas>
                  </div>
                </div>
              </div> --}}
              <!-- /Radar Chart -->

              <!-- Polar Area Chart -->
              {{-- <div class="col-lg-6 col-12 mb-4">
                <div class="card">
                  <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Average Skills</h5>
                    <div class="card-header-elements ms-auto py-0 dropdown">
                      <button
                        type="button"
                        class="btn dropdown-toggle hide-arrow p-0"
                        id="heat-chart-dd"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="mdi mdi-dots-vertical"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="heat-chart-dd">
                        <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                        <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                        <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <canvas id="polarChart" class="chartjs" data-height="337"></canvas>
                  </div>
                </div>
              </div> --}}
              <!-- /Polar Area Chart -->

              <!-- Bubble Chart -->
              {{-- <div class="col-12 mb-4">
                <div class="card">
                  <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Bubble Chart</h5>
                    <div class="card-header-elements ms-auto py-0">
                      <h5 class="mb-0 me-3">$ 100,000</h5>
                      <span class="badge bg-label-secondary rounded-pill">
                        <i class="mdi mdi-arrow-down mdi-14px text-danger"></i>
                        <span class="align-middle">20%</span>
                      </span>
                    </div>
                  </div>
                  <div class="card-body">
                    <canvas id="bubbleChart" class="chartjs" data-height="500"></canvas>
                  </div>
                </div>
              </div> --}}
              <!-- /Bubble Chart -->

              <!-- Line Area Charts -->
              {{-- <div class="col-12 mb-4">
                <div class="card">
                  <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Data Science</h5>
                    <div class="card-header-elements py-0 ms-auto">
                      <div class="dropdown">
                        <button
                          type="button"
                          class="btn dropdown-toggle p-0"
                          data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="mdi mdi-calendar-month-outline"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Today</a>
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Yesterday</a
                            >
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Last 7 Days</a
                            >
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Last 30 Days</a
                            >
                          </li>
                          <li>
                            <hr class="dropdown-divider" />
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Current Month</a
                            >
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Last Month</a
                            >
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="card-body pt-2">
                    <canvas id="lineAreaChart" class="chartjs" data-height="450"></canvas>
                  </div>
                </div>
              </div> --}}
              <!-- /Line Area Charts -->

              <!-- Bar Charts -->
              {{-- <div class="col-xl-12 col-12 mb-4">
                <div class="card">
                  <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Latest Statistics</h5>
                    <div class="card-action-element ms-auto py-0">
                      <div class="dropdown">
                        <button
                          type="button"
                          class="btn dropdown-toggle px-0"
                          data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="mdi mdi-calendar-month-outline"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Today</a>
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Yesterday</a
                            >
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Last 7 Days</a
                            >
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Last 30 Days</a
                            >
                          </li>
                          <li>
                            <hr class="dropdown-divider" />
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Current Month</a
                            >
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Last Month</a
                            >
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <canvas id="barChart" class="chartjs" data-height="400"></canvas>
                  </div>
                </div>
              </div> --}}
              <!-- /Bar Charts -->

              <!-- Horizontal Bar Charts -->
              {{-- <div class="col-xl-6 col-12 mb-4">
                <div class="card">
                  <div class="card-header header-elements">
                    <div class="d-flex flex-column">
                      <h5 class="card-title mb-1">Balance</h5>
                      <p class="text-muted mb-0">$74,123</p>
                    </div>
                    <div class="card-action-element ms-auto py-0">
                      <div class="dropdown">
                        <button
                          type="button"
                          class="btn dropdown-toggle px-0"
                          data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="mdi mdi-calendar-month-outline"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Today</a>
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Yesterday</a
                            >
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Last 7 Days</a
                            >
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Last 30 Days</a
                            >
                          </li>
                          <li>
                            <hr class="dropdown-divider" />
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Current Month</a
                            >
                          </li>
                          <li>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                              >Last Month</a
                            >
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <canvas id="horizontalBarChart" class="chartjs" data-height="400"></canvas>
                  </div>
                </div>
              </div> --}}
              <!-- /Horizontal Bar Charts -->

              <!-- Doughnut Chart -->
              {{-- <div class="col-lg-4 col-12 mb-4">
                <div class="card">
                  <h5 class="card-header">User by Devices</h5>
                  <div class="card-body">
                    <canvas id="doughnutChart" class="chartjs mb-4" data-height="350"></canvas>
                    <ul class="doughnut-legend d-flex justify-content-around ps-0 mb-2 pt-1">
                      <li class="ct-series-0 d-flex flex-column">
                        <h5 class="mb-0">Desktop</h5>
                        <span
                          class="badge badge-dot my-2 cursor-pointer rounded-pill"
                          style="background-color: rgb(102, 110, 232); width: 35px; height: 6px"></span>
                        <div class="text-muted">80 %</div>
                      </li>
                      <li class="ct-series-1 d-flex flex-column">
                        <h5 class="mb-0">Tablet</h5>
                        <span
                          class="badge badge-dot my-2 cursor-pointer rounded-pill"
                          style="background-color: rgb(40, 208, 148); width: 35px; height: 6px"></span>
                        <div class="text-muted">10 %</div>
                      </li>
                      <li class="ct-series-2 d-flex flex-column">
                        <h5 class="mb-0">Mobile</h5>
                        <span
                          class="badge badge-dot my-2 cursor-pointer rounded-pill"
                          style="background-color: rgb(253, 172, 52); width: 35px; height: 6px"></span>
                        <div class="text-muted">10 %</div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div> --}}
              <!-- /Doughnut Chart -->

              <!-- Scatter Chart -->
              {{-- <div class="col-lg-8 col-12 mb-4">
                <div class="card">
                  <div class="card-header flex-nowrap header-elements">
                    <h5 class="card-title mb-0">New Product Data</h5>
                    <div class="card-header-elements ms-auto py-0 d-none d-sm-block">
                      <div class="btn-group" role="group" aria-label="radio toggle button group">
                        <input type="radio" class="btn-check" name="btnradio" id="dailyRadio" checked />
                        <label class="btn btn-outline-secondary" for="dailyRadio">Daily</label>

                        <input type="radio" class="btn-check" name="btnradio" id="monthlyRadio" />
                        <label class="btn btn-outline-secondary" for="monthlyRadio">Monthly</label>

                        <input type="radio" class="btn-check" name="btnradio" id="yearlyRadio" />
                        <label class="btn btn-outline-secondary" for="yearlyRadio">Yearly</label>
                      </div>
                    </div>
                  </div>
                  <div class="card-body pt-2">
                    <canvas id="scatterChart" class="chartjs" data-height="435"></canvas>
                  </div>
                </div>
              </div> --}}
              <!-- /Scatter Chart -->
            </div>
          </div>
    </div>
@endsection
@section('js')
<script src="{{ asset('assets/vendor/libs/chartjs/chartjs.js') }}"></script>



<!-- Page JS -->
{{-- <script src="{{ asset('assets/js/charts-chartjs.js') }}"></script> --}}

<script>
    /**
 * Charts ChartsJS
 */
'use strict';

(function () {
  // Color Variables
  const purpleColor = '#836AF9',
    yellowColor = '#ffe800',
    cyanColor = '#28dac6',
    orangeColor = '#FF8132',
    orangeLightColor = '#ffcf5c',
    oceanBlueColor = '#299AFF',
    greyColor = '#4F5D70',
    greyLightColor = '#EDF1F4',
    blueColor = '#2B9AFF',
    blueLightColor = '#84D0FF';

  let cardColor, headingColor, labelColor, borderColor, legendColor;

  if (isDarkStyle) {
    cardColor = config.colors_dark.cardColor;
    headingColor = config.colors_dark.headingColor;
    labelColor = config.colors_dark.textMuted;
    legendColor = config.colors_dark.bodyColor;
    borderColor = config.colors_dark.borderColor;
  } else {
    cardColor = config.colors.cardColor;
    headingColor = config.colors.headingColor;
    labelColor = config.colors.textMuted;
    legendColor = config.colors.bodyColor;
    borderColor = config.colors.borderColor;
  }

  // Set height according to their data-height
  // --------------------------------------------------------------------
  const chartList = document.querySelectorAll('.chartjs');
  chartList.forEach(function (chartListItem) {
    chartListItem.height = chartListItem.dataset.height;
  });

  // Bar Chart
  // --------------------------------------------------------------------
//   const barChart = document.getElementById('barChart');
//   if (barChart) {
//     const barChartVar = new Chart(barChart, {
//       type: 'bar',
//       data: {
//         labels: [
//           '7/12',
//           '8/12',
//           '9/12',
//           '10/12',
//           '11/12',
//           '12/12',
//           '13/12',
//           '14/12',
//           '15/12',
//           '16/12',
//           '17/12',
//           '18/12',
//           '19/12'
//         ],
//         datasets: [
//           {
//             data: [275, 90, 190, 205, 125, 85, 55, 87, 127, 150, 230, 280, 190],
//             backgroundColor: orangeLightColor,
//             borderColor: 'transparent',
//             maxBarThickness: 15,
//             borderRadius: {
//               topRight: 15,
//               topLeft: 15
//             }
//           }
//         ]
//       },
//       options: {
//         responsive: true,
//         maintainAspectRatio: false,
//         animation: {
//           duration: 500
//         },
//         plugins: {
//           tooltip: {
//             rtl: isRtl,
//             backgroundColor: cardColor,
//             titleColor: headingColor,
//             bodyColor: legendColor,
//             borderWidth: 1,
//             borderColor: borderColor
//           },
//           legend: {
//             display: false
//           }
//         },
//         scales: {
//           x: {
//             grid: {
//               color: borderColor,
//               drawBorder: false,
//               borderColor: borderColor
//             },
//             ticks: {
//               color: labelColor
//             }
//           },
//           y: {
//             min: 0,
//             max: 400,
//             grid: {
//               color: borderColor,
//               drawBorder: false,
//               borderColor: borderColor
//             },
//             ticks: {
//               stepSize: 100,
//               color: labelColor
//             }
//           }
//         }
//       }
//     });
//   }

const barLabels = {!! json_encode($monthlyLabels) !!};
  const devBarData = {!! json_encode($monthlyDev) !!};
  const marketingBarData = {!! json_encode($monthlyMarketing) !!};

  const barChart = document.getElementById('barChart');
  if (barChart) {
    new Chart(barChart, {
      type: 'bar',
      data: {
        labels: barLabels,
        datasets: [
          {
            label: 'Development',
            data: devBarData,
            backgroundColor: config.colors.primary,
            borderColor: 'transparent',
            maxBarThickness: 15,
            borderRadius: {
              topLeft: 15,
              topRight: 15
            }
          },
          {
            label: 'Marketing',
            data: marketingBarData,
            backgroundColor: config.colors.warning,
            borderColor: 'transparent',
            maxBarThickness: 15,
            borderRadius: {
              topLeft: 15,
              topRight: 15
            }
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 500
        },
        plugins: {
          tooltip: {
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          },
          legend: {
            display: true,
            position: 'top',
            labels: {
              color: legendColor,
              usePointStyle: true
            }
          }
        },
        scales: {
          x: {
            grid: {
              color: borderColor,
              drawBorder: false
            },
            ticks: {
              color: labelColor
            }
          },
          y: {
            beginAtZero: true,
            grid: {
              color: borderColor,
              drawBorder: false
            },
            ticks: {
              stepSize: 100,
              color: labelColor
            }
          }
        }
      }
    });
  }

  // Horizontal Bar Chart
  // --------------------------------------------------------------------

//   const horizontalBarChart = document.getElementById('horizontalBarChart');
//   if (horizontalBarChart) {
//     const horizontalBarChartVar = new Chart(horizontalBarChart, {
//       type: 'bar',
//       data: {
//         labels: ['MON', 'TUE', 'WED ', 'THU', 'FRI', 'SAT', 'SUN'],
//         datasets: [
//           {
//             data: [710, 350, 470, 580, 230, 460, 120],
//             backgroundColor: cyanColor,
//             borderColor: 'transparent',
//             maxBarThickness: 15
//           }
//         ]
//       },
//       options: {
//         indexAxis: 'y',
//         responsive: true,
//         maintainAspectRatio: false,
//         animation: {
//           duration: 500
//         },
//         elements: {
//           bar: {
//             borderRadius: {
//               topRight: 15,
//               bottomRight: 15
//             }
//           }
//         },
//         plugins: {
//           tooltip: {
//             rtl: isRtl,
//             backgroundColor: cardColor,
//             titleColor: headingColor,
//             bodyColor: legendColor,
//             borderWidth: 1,
//             borderColor: borderColor
//           },
//           legend: {
//             display: false
//           }
//         },
//         scales: {
//           x: {
//             min: 0,
//             grid: {
//               color: borderColor,
//               borderColor: borderColor
//             },
//             ticks: {
//               color: labelColor
//             }
//           },
//           y: {
//             grid: {
//               borderColor: borderColor,
//               display: false,
//               drawBorder: false
//             },
//             ticks: {
//               color: labelColor
//             }
//           }
//         }
//       }
//     });
//   }

  // Line Chart
  // --------------------------------------------------------------------

//   const lineChart = document.getElementById('lineChart');
//   if (lineChart) {
//     const lineChartVar = new Chart(lineChart, {
//       type: 'line',
//       data: {
//         labels: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140],
//         datasets: [
//           {
//             data: [80, 150, 180, 270, 210, 160, 160, 202, 265, 210, 270, 255, 290, 360, 375],
//             label: 'Europe',
//             borderColor: config.colors.primary,
//             tension: 0.5,
//             pointStyle: 'circle',
//             backgroundColor: config.colors.primary,
//             fill: false,
//             pointRadius: 1,
//             pointHoverRadius: 5,
//             pointHoverBorderWidth: 5,
//             pointBorderColor: 'transparent',
//             pointHoverBorderColor: cardColor,
//             pointHoverBackgroundColor: config.colors.primary
//           },
//           {
//             data: [80, 125, 105, 130, 215, 195, 140, 160, 230, 300, 220, 170, 210, 200, 280],
//             label: 'Asia',
//             borderColor: config.colors.warning,
//             tension: 0.5,
//             pointStyle: 'circle',
//             backgroundColor: config.colors.warning,
//             fill: false,
//             pointRadius: 1,
//             pointHoverRadius: 5,
//             pointHoverBorderWidth: 5,
//             pointBorderColor: 'transparent',
//             pointHoverBorderColor: cardColor,
//             pointHoverBackgroundColor: config.colors.warning
//           },
//           {
//             data: [80, 99, 82, 90, 115, 115, 74, 75, 130, 155, 125, 90, 140, 130, 180],
//             label: 'Africa',
//             borderColor: yellowColor,
//             tension: 0.5,
//             pointStyle: 'circle',
//             backgroundColor: yellowColor,
//             fill: false,
//             pointRadius: 1,
//             pointHoverRadius: 5,
//             pointHoverBorderWidth: 5,
//             pointBorderColor: 'transparent',
//             pointHoverBorderColor: cardColor,
//             pointHoverBackgroundColor: yellowColor
//           }
//         ]
//       },
//       options: {
//         responsive: true,
//         maintainAspectRatio: false,
//         scales: {
//           x: {
//             grid: {
//               color: borderColor,
//               drawBorder: false,
//               borderColor: borderColor
//             },
//             ticks: {
//               color: labelColor
//             }
//           },
//           y: {
//             scaleLabel: {
//               display: true
//             },
//             min: 0,
//             max: 400,
//             ticks: {
//               color: labelColor,
//               stepSize: 100
//             },
//             grid: {
//               color: borderColor,
//               drawBorder: false,
//               borderColor: borderColor
//             }
//           }
//         },
//         plugins: {
//           tooltip: {
//             // Updated default tooltip UI
//             rtl: isRtl,
//             backgroundColor: cardColor,
//             titleColor: headingColor,
//             bodyColor: legendColor,
//             borderWidth: 1,
//             borderColor: borderColor
//           },
//           legend: {
//             position: 'top',
//             align: 'start',
//             rtl: isRtl,
//             labels: {
//               font: {
//                 family: 'Inter'
//               },
//               usePointStyle: true,
//               padding: 35,
//               boxWidth: 6,
//               boxHeight: 6,
//               color: legendColor
//             }
//           }
//         }
//       }
//     });
//   }
console.log({!! json_encode($datasets['Marketing']) !!});

const labels = {!! json_encode($labels) !!};
  const developmentData = {!! json_encode($datasets['Development']) !!};
  const marketingData = {!! json_encode($datasets['Marketing']) !!};

  const lineChart = document.getElementById('lineChart');
  if (lineChart) {
    new Chart(lineChart, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Development ${{ $developmentTotal }}',
            data: developmentData,
            borderColor: config.colors.primary,
            backgroundColor: config.colors.primary,
            tension: 0.5,
            fill: false
          },
          {
            label: 'Marketing ${{ $marketingTotal }}',
            data: marketingData,
            borderColor: config.colors.warning,
            backgroundColor: config.colors.warning,
            tension: 0.5,
            fill: false
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            grid: {
              color: borderColor,
              drawBorder: false
            },
            ticks: {
              color: labelColor
            }
          },
          y: {
            min: 0,
            ticks: {
              color: labelColor,
              stepSize: 100
            },
            grid: {
              color: borderColor,
              drawBorder: false
            }
          }
        },
        plugins: {
          tooltip: {
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          },
          legend: {
            position: 'top',
            labels: {
              usePointStyle: true,
              color: legendColor
            }
          }
        }
      }
    });
  }

  // Radar Chart
  // --------------------------------------------------------------------

//   const radarChart = document.getElementById('radarChart');
//   if (radarChart) {
//     // For radar gradient color
//     const gradientBlue = radarChart.getContext('2d').createLinearGradient(0, 0, 0, 150);
//     gradientBlue.addColorStop(0, 'rgba(85, 85, 255, 0.9)');
//     gradientBlue.addColorStop(1, 'rgba(151, 135, 255, 0.8)');

//     const gradientRed = radarChart.getContext('2d').createLinearGradient(0, 0, 0, 150);
//     gradientRed.addColorStop(0, 'rgba(255, 85, 184, 0.9)');
//     gradientRed.addColorStop(1, 'rgba(255, 135, 135, 0.8)');

//     const radarChartVar = new Chart(radarChart, {
//       type: 'radar',
//       data: {
//         labels: ['STA', 'STR', 'AGI', 'VIT', 'CHA', 'INT'],
//         datasets: [
//           {
//             label: 'DontÃ© Panlin',
//             data: [25, 59, 90, 81, 60, 82],
//             fill: true,
//             pointStyle: 'dash',
//             backgroundColor: gradientRed,
//             borderColor: 'transparent',
//             pointBorderColor: 'transparent'
//           },
//           {
//             label: 'Mireska Sunbreeze',
//             data: [40, 100, 40, 90, 40, 90],
//             fill: true,
//             pointStyle: 'dash',
//             backgroundColor: gradientBlue,
//             borderColor: 'transparent',
//             pointBorderColor: 'transparent'
//           }
//         ]
//       },
//       options: {
//         responsive: true,
//         maintainAspectRatio: false,
//         animation: {
//           duration: 500
//         },
//         scales: {
//           r: {
//             ticks: {
//               maxTicksLimit: 1,
//               display: false,
//               color: labelColor
//             },
//             grid: {
//               color: borderColor
//             },
//             angleLines: { color: borderColor },
//             pointLabels: {
//               color: labelColor
//             }
//           }
//         },
//         plugins: {
//           legend: {
//             rtl: isRtl,
//             position: 'top',
//             labels: {
//               padding: 25,
//               color: legendColor,
//               font: {
//                 family: 'Inter'
//               }
//             }
//           },
//           tooltip: {
//             // Updated default tooltip UI
//             rtl: isRtl,
//             backgroundColor: cardColor,
//             titleColor: headingColor,
//             bodyColor: legendColor,
//             borderWidth: 1,
//             borderColor: borderColor
//           }
//         }
//       }
//     });
//   }

  // Polar Chart
  // --------------------------------------------------------------------

//   const polarChart = document.getElementById('polarChart');
//   if (polarChart) {
//     const polarChartVar = new Chart(polarChart, {
//       type: 'polarArea',
//       data: {
//         labels: ['Africa', 'Asia', 'Europe', 'America', 'Antarctica', 'Australia'],
//         datasets: [
//           {
//             label: 'Population (millions)',
//             backgroundColor: [purpleColor, yellowColor, orangeColor, oceanBlueColor, greyColor, cyanColor],
//             data: [19, 17.5, 15, 13.5, 11, 9],
//             borderWidth: 0
//           }
//         ]
//       },
//       options: {
//         responsive: true,
//         maintainAspectRatio: false,
//         animation: {
//           duration: 500
//         },
//         scales: {
//           r: {
//             ticks: {
//               display: false,
//               color: labelColor
//             },
//             grid: {
//               display: false
//             }
//           }
//         },
//         plugins: {
//           tooltip: {
//             // Updated default tooltip UI
//             rtl: isRtl,
//             backgroundColor: cardColor,
//             titleColor: headingColor,
//             bodyColor: legendColor,
//             borderWidth: 1,
//             borderColor: borderColor
//           },
//           legend: {
//             rtl: isRtl,
//             position: 'right',
//             labels: {
//               usePointStyle: true,
//               padding: 25,
//               boxWidth: 8,
//               boxHeight: 8,
//               color: legendColor,
//               font: {
//                 family: 'Inter'
//               }
//             }
//           }
//         }
//       }
//     });
//   }

  // Bubble Chart
  // --------------------------------------------------------------------

//   const bubbleChart = document.getElementById('bubbleChart');
//   if (bubbleChart) {
//     const bubbleChartVar = new Chart(bubbleChart, {
//       type: 'bubble',
//       data: {
//         animation: {
//           duration: 10000
//         },
//         datasets: [
//           {
//             label: 'Dataset 1',
//             backgroundColor: purpleColor,
//             borderColor: purpleColor,
//             data: [
//               {
//                 x: 20,
//                 y: 74,
//                 r: 10
//               },
//               {
//                 x: 10,
//                 y: 110,
//                 r: 5
//               },
//               {
//                 x: 30,
//                 y: 165,
//                 r: 7
//               },
//               {
//                 x: 40,
//                 y: 200,
//                 r: 20
//               },
//               {
//                 x: 90,
//                 y: 185,
//                 r: 7
//               },
//               {
//                 x: 50,
//                 y: 240,
//                 r: 7
//               },
//               {
//                 x: 60,
//                 y: 275,
//                 r: 10
//               },
//               {
//                 x: 70,
//                 y: 305,
//                 r: 5
//               },
//               {
//                 x: 80,
//                 y: 325,
//                 r: 4
//               },
//               {
//                 x: 100,
//                 y: 310,
//                 r: 5
//               },
//               {
//                 x: 110,
//                 y: 240,
//                 r: 5
//               },
//               {
//                 x: 120,
//                 y: 270,
//                 r: 7
//               },
//               {
//                 x: 130,
//                 y: 300,
//                 r: 6
//               }
//             ]
//           },
//           {
//             label: 'Dataset 2',
//             backgroundColor: yellowColor,
//             borderColor: yellowColor,
//             data: [
//               {
//                 x: 30,
//                 y: 72,
//                 r: 5
//               },
//               {
//                 x: 40,
//                 y: 110,
//                 r: 7
//               },
//               {
//                 x: 20,
//                 y: 135,
//                 r: 6
//               },
//               {
//                 x: 10,
//                 y: 160,
//                 r: 12
//               },
//               {
//                 x: 50,
//                 y: 285,
//                 r: 5
//               },
//               {
//                 x: 60,
//                 y: 235,
//                 r: 5
//               },
//               {
//                 x: 70,
//                 y: 275,
//                 r: 7
//               },
//               {
//                 x: 80,
//                 y: 290,
//                 r: 4
//               },
//               {
//                 x: 90,
//                 y: 250,
//                 r: 10
//               },
//               {
//                 x: 100,
//                 y: 220,
//                 r: 7
//               },
//               {
//                 x: 120,
//                 y: 230,
//                 r: 4
//               },
//               {
//                 x: 110,
//                 y: 320,
//                 r: 15
//               },
//               {
//                 x: 130,
//                 y: 330,
//                 r: 7
//               }
//             ]
//           }
//         ]
//       },
//       options: {
//         responsive: true,
//         maintainAspectRatio: false,

//         scales: {
//           x: {
//             min: 0,
//             max: 140,
//             grid: {
//               color: borderColor,
//               drawBorder: false,
//               borderColor: borderColor
//             },
//             ticks: {
//               stepSize: 10,
//               color: labelColor
//             }
//           },
//           y: {
//             min: 0,
//             max: 400,
//             grid: {
//               color: borderColor,
//               drawBorder: false,
//               borderColor: borderColor
//             },
//             ticks: {
//               stepSize: 100,
//               color: labelColor
//             }
//           }
//         },
//         plugins: {
//           legend: {
//             display: false
//           },
//           tooltip: {
//             // Updated default tooltip UI
//             rtl: isRtl,
//             backgroundColor: cardColor,
//             titleColor: headingColor,
//             bodyColor: legendColor,
//             borderWidth: 1,
//             borderColor: borderColor
//           }
//         }
//       }
//     });
//   }

  // LineArea Chart
  // --------------------------------------------------------------------

//   const lineAreaChart = document.getElementById('lineAreaChart');
//   if (lineAreaChart) {
//     const lineAreaChartVar = new Chart(lineAreaChart, {
//       type: 'line',
//       data: {
//         labels: [
//           '7/12',
//           '8/12',
//           '9/12',
//           '10/12',
//           '11/12',
//           '12/12',
//           '13/12',
//           '14/12',
//           '15/12',
//           '16/12',
//           '17/12',
//           '18/12',
//           '19/12',
//           '20/12',
//           ''
//         ],
//         datasets: [
//           {
//             label: 'Africa',
//             data: [40, 55, 45, 75, 65, 55, 70, 60, 100, 98, 90, 120, 125, 140, 155],
//             tension: 0,
//             fill: true,
//             backgroundColor: blueColor,
//             pointStyle: 'circle',
//             borderColor: 'transparent',
//             pointRadius: 0.5,
//             pointHoverRadius: 5,
//             pointHoverBorderWidth: 5,
//             pointBorderColor: 'transparent',
//             pointHoverBackgroundColor: blueColor,
//             pointHoverBorderColor: cardColor
//           },
//           {
//             label: 'Asia',
//             data: [70, 85, 75, 150, 100, 140, 110, 105, 160, 150, 125, 190, 200, 240, 275],
//             tension: 0,
//             fill: true,
//             backgroundColor: blueLightColor,
//             pointStyle: 'circle',
//             borderColor: 'transparent',
//             pointRadius: 0.5,
//             pointHoverRadius: 5,
//             pointHoverBorderWidth: 5,
//             pointBorderColor: 'transparent',
//             pointHoverBackgroundColor: blueLightColor,
//             pointHoverBorderColor: cardColor
//           },
//           {
//             label: 'Europe',
//             data: [240, 195, 160, 215, 185, 215, 185, 200, 250, 210, 195, 250, 235, 300, 315],
//             tension: 0,
//             fill: true,
//             backgroundColor: greyLightColor,
//             pointStyle: 'circle',
//             borderColor: 'transparent',
//             pointRadius: 0.5,
//             pointHoverRadius: 5,
//             pointHoverBorderWidth: 5,
//             pointBorderColor: 'transparent',
//             pointHoverBackgroundColor: greyLightColor,
//             pointHoverBorderColor: cardColor
//           }
//         ]
//       },
//       options: {
//         responsive: true,
//         maintainAspectRatio: false,
//         plugins: {
//           legend: {
//             position: 'top',
//             rtl: isRtl,
//             align: 'start',
//             labels: {
//               usePointStyle: true,
//               padding: 35,
//               boxWidth: 6,
//               boxHeight: 6,
//               color: legendColor,
//               font: {
//                 family: 'Inter'
//               }
//             }
//           },
//           tooltip: {
//             // Updated default tooltip UI
//             rtl: isRtl,
//             backgroundColor: cardColor,
//             titleColor: headingColor,
//             bodyColor: legendColor,
//             borderWidth: 1,
//             borderColor: borderColor
//           }
//         },
//         scales: {
//           x: {
//             grid: {
//               color: 'transparent',
//               borderColor: borderColor
//             },
//             ticks: {
//               color: labelColor
//             }
//           },
//           y: {
//             min: 0,
//             max: 400,
//             grid: {
//               color: 'transparent',
//               borderColor: borderColor
//             },
//             ticks: {
//               stepSize: 100,
//               color: labelColor
//             }
//           }
//         }
//       }
//     });
//   }

  // Doughnut Chart
  // --------------------------------------------------------------------

//   const doughnutChart = document.getElementById('doughnutChart');
//   if (doughnutChart) {
//     const doughnutChartVar = new Chart(doughnutChart, {
//       type: 'doughnut',
//       data: {
//         labels: ['Tablet', 'Mobile', 'Desktop'],
//         datasets: [
//           {
//             data: [10, 10, 80],
//             backgroundColor: [cyanColor, orangeLightColor, config.colors.primary],
//             borderWidth: 0,
//             pointStyle: 'rectRounded'
//           }
//         ]
//       },
//       options: {
//         responsive: true,
//         animation: {
//           duration: 500
//         },
//         cutout: '68%',
//         plugins: {
//           legend: {
//             display: false
//           },
//           tooltip: {
//             callbacks: {
//               label: function (context) {
//                 const label = context.labels || '',
//                   value = context.parsed;
//                 const output = ' ' + label + ' : ' + value + ' %';
//                 return output;
//               }
//             },
//             // Updated default tooltip UI
//             rtl: isRtl,
//             backgroundColor: cardColor,
//             titleColor: headingColor,
//             bodyColor: legendColor,
//             borderWidth: 1,
//             borderColor: borderColor
//           }
//         }
//       }
//     });
//   }

  // Scatter Chart
  // --------------------------------------------------------------------

//   const scatterChart = document.getElementById('scatterChart');
//   if (scatterChart) {
//     const scatterChartVar = new Chart(scatterChart, {
//       type: 'scatter',
//       data: {
//         datasets: [
//           {
//             label: 'iPhone',
//             data: [
//               {
//                 x: 72,
//                 y: 225
//               },
//               {
//                 x: 81,
//                 y: 270
//               },
//               {
//                 x: 90,
//                 y: 230
//               },
//               {
//                 x: 103,
//                 y: 305
//               },
//               {
//                 x: 103,
//                 y: 245
//               },
//               {
//                 x: 108,
//                 y: 275
//               },
//               {
//                 x: 110,
//                 y: 290
//               },
//               {
//                 x: 111,
//                 y: 315
//               },
//               {
//                 x: 109,
//                 y: 350
//               },
//               {
//                 x: 116,
//                 y: 340
//               },
//               {
//                 x: 113,
//                 y: 260
//               },
//               {
//                 x: 117,
//                 y: 275
//               },
//               {
//                 x: 117,
//                 y: 295
//               },
//               {
//                 x: 126,
//                 y: 280
//               },
//               {
//                 x: 127,
//                 y: 340
//               },
//               {
//                 x: 133,
//                 y: 330
//               }
//             ],
//             backgroundColor: config.colors.primary,
//             borderColor: 'transparent',
//             pointBorderWidth: 2,
//             pointHoverBorderWidth: 2,
//             pointRadius: 5
//           },
//           {
//             label: 'Samsung Note',
//             data: [
//               {
//                 x: 13,
//                 y: 95
//               },
//               {
//                 x: 22,
//                 y: 105
//               },
//               {
//                 x: 17,
//                 y: 115
//               },
//               {
//                 x: 19,
//                 y: 130
//               },
//               {
//                 x: 21,
//                 y: 125
//               },
//               {
//                 x: 35,
//                 y: 125
//               },
//               {
//                 x: 13,
//                 y: 155
//               },
//               {
//                 x: 21,
//                 y: 165
//               },
//               {
//                 x: 25,
//                 y: 155
//               },
//               {
//                 x: 18,
//                 y: 190
//               },
//               {
//                 x: 26,
//                 y: 180
//               },
//               {
//                 x: 43,
//                 y: 180
//               },
//               {
//                 x: 53,
//                 y: 202
//               },
//               {
//                 x: 61,
//                 y: 165
//               },
//               {
//                 x: 67,
//                 y: 225
//               }
//             ],
//             backgroundColor: yellowColor,
//             borderColor: 'transparent',
//             pointRadius: 5
//           },
//           {
//             label: 'OnePlus',
//             data: [
//               {
//                 x: 70,
//                 y: 195
//               },
//               {
//                 x: 72,
//                 y: 270
//               },
//               {
//                 x: 98,
//                 y: 255
//               },
//               {
//                 x: 100,
//                 y: 215
//               },
//               {
//                 x: 87,
//                 y: 240
//               },
//               {
//                 x: 94,
//                 y: 280
//               },
//               {
//                 x: 99,
//                 y: 300
//               },
//               {
//                 x: 102,
//                 y: 290
//               },
//               {
//                 x: 110,
//                 y: 275
//               },
//               {
//                 x: 111,
//                 y: 250
//               },
//               {
//                 x: 94,
//                 y: 280
//               },
//               {
//                 x: 92,
//                 y: 340
//               },
//               {
//                 x: 100,
//                 y: 335
//               },
//               {
//                 x: 108,
//                 y: 330
//               }
//             ],
//             backgroundColor: cyanColor,
//             borderColor: 'transparent',
//             pointBorderWidth: 2,
//             pointHoverBorderWidth: 2,
//             pointRadius: 5
//           }
//         ]
//       },
//       options: {
//         responsive: true,
//         maintainAspectRatio: false,
//         animation: {
//           duration: 800
//         },
//         plugins: {
//           legend: {
//             position: 'top',
//             rtl: isRtl,
//             align: 'start',
//             labels: {
//               usePointStyle: true,
//               padding: 25,
//               boxWidth: 6,
//               boxHeight: 6,
//               color: legendColor,
//               font: {
//                 family: 'Inter'
//               }
//             }
//           },
//           tooltip: {
//             // Updated default tooltip UI
//             rtl: isRtl,
//             backgroundColor: cardColor,
//             titleColor: headingColor,
//             bodyColor: legendColor,
//             borderWidth: 1,
//             borderColor: borderColor
//           }
//         },
//         scales: {
//           x: {
//             min: 0,
//             max: 140,
//             grid: {
//               color: borderColor,
//               drawTicks: false,
//               drawBorder: false,
//               borderColor: borderColor
//             },
//             ticks: {
//               stepSize: 10,
//               color: labelColor
//             }
//           },
//           y: {
//             min: 0,
//             max: 400,
//             grid: {
//               color: borderColor,
//               drawTicks: false,
//               drawBorder: false,
//               borderColor: borderColor
//             },
//             ticks: {
//               stepSize: 100,
//               color: labelColor
//             }
//           }
//         }
//       }
//     });
//   }
})();
</script>
@endsection