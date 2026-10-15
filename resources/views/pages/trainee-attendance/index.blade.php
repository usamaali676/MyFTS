@extends('layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/trainee-enhance.css') }}" />
@endsection
@section('content')
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y trainee-enhanced">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="py-3 mb-4">
                    <span class="text-muted fw-light">Trainee/{{ $trainee->name }}/</span> Attendance
                </h4>
                <a href="{{ route('trainee.index') }}" class="btn btn-outline-secondary">Back to Trainees</a>
              </div>

              <div class="row mb-4 g-4">
                <div class="col-sm-6 col-lg-3">
                  <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                      <span class="badge rounded-pill bg-label-success p-2"><i class="mdi mdi-check-circle-outline mdi-24px"></i></span>
                      <div>
                        <h5 class="mb-0">{{ $onTimeCount }}</h5>
                        <small class="text-muted">On-Time</small>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                  <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                      <span class="badge rounded-pill bg-label-warning p-2"><i class="mdi mdi-clock-alert-outline mdi-24px"></i></span>
                      <div>
                        <h5 class="mb-0">{{ $lateCount }}</h5>
                        <small class="text-muted">Late</small>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                  <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                      <span class="badge rounded-pill bg-label-danger p-2"><i class="mdi mdi-close-circle-outline mdi-24px"></i></span>
                      <div>
                        <h5 class="mb-0">{{ $absentCount }}</h5>
                        <small class="text-muted">Absent</small>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                  <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                      <span class="badge rounded-pill bg-label-primary p-2"><i class="mdi mdi-chart-donut mdi-24px"></i></span>
                      <div>
                        <h5 class="mb-0">{{ $attendanceRate !== null ? $attendanceRate . '%' : '-' }}</h5>
                        <small class="text-muted">Attendance Rate</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <a href="{{ route('traineeattendance.index', ['trainee' => $trainee->id, 'month' => $month->copy()->subMonth()->format('Y-m')]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="mdi mdi-chevron-left"></i> Prev
                    </a>
                    <h5 class="mb-0">{{ $month->format('F Y') }}</h5>
                    <a href="{{ route('traineeattendance.index', ['trainee' => $trainee->id, 'month' => $month->copy()->addMonth()->format('Y-m')]) }}" class="btn btn-sm btn-outline-secondary">
                        Next <i class="mdi mdi-chevron-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle mb-3">
                            <thead>
                                <tr>
                                    <th>Sun</th>
                                    <th>Mon</th>
                                    <th>Tue</th>
                                    <th>Wed</th>
                                    <th>Thu</th>
                                    <th>Fri</th>
                                    <th>Sat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($weeks as $week)
                                    <tr>
                                        @foreach ($week as $day)
                                            @php
                                                $key = $day->format('Y-m-d');
                                                $inMonth = $day->month === $month->month;
                                                $record = $records->get($key);
                                                $isToday = $day->isToday();

                                                $cellClass = 'text-muted';
                                                $label = null;
                                                if ($inMonth && $record) {
                                                    if ($record->present && !$record->late) {
                                                        $cellClass = 'bg-label-success';
                                                        $label = 'On-Time';
                                                    } elseif ($record->present && $record->late) {
                                                        $cellClass = 'bg-label-warning';
                                                        $label = 'Late';
                                                    } else {
                                                        $cellClass = 'bg-label-danger';
                                                        $label = 'Absent';
                                                    }
                                                } elseif (!$inMonth) {
                                                    $cellClass = 'text-muted bg-light';
                                                }
                                            @endphp
                                            <td class="{{ $cellClass }}" style="height: 70px; vertical-align: top; {{ $isToday ? 'outline: 2px solid var(--bs-primary); outline-offset: -2px;' : '' }}">
                                                <div class="fw-semibold">{{ $day->day }}</div>
                                                @if($label)
                                                    <small class="d-block">{{ $label }}</small>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap gap-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill bg-label-success">&nbsp;</span> On-Time
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill bg-label-warning">&nbsp;</span> Late
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill bg-label-danger">&nbsp;</span> Absent
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill bg-light border">&nbsp;</span> No Record
                        </div>
                    </div>
                </div>
              </div>
            </div>
            <div class="content-backdrop fade"></div>
          </div>
@endsection
