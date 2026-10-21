@extends('layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/profile-enhance.css') }}" />
    <style>
        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            #layout-menu, .layout-navbar, .no-print, .content-backdrop, .layout-overlay { display: none !important; }
            .layout-page, .layout-content-navbar .layout-page { padding: 0 !important; margin: 0 !important; }
            .container-p-y { padding: 0 !important; }
            .card { box-shadow: none !important; }
        }
    </style>
@endsection
@section('content')
    @php
        $breakTypes = [
            'mealBreak'  => ['label' => 'Meal Break',  'icon' => 'mdi-food-fork-drink', 'color' => 'bg-label-warning'],
            '2ndBreak'   => ['label' => '2nd Break',   'icon' => 'mdi-coffee',          'color' => 'bg-label-info'],
            'smokeBreak' => ['label' => 'Smoke Break', 'icon' => 'mdi-smoking',         'color' => 'bg-label-secondary'],
        ];
        $typeOf = fn ($key) => $breakTypes[$key] ?? ['label' => $key ?: 'Break', 'icon' => 'mdi-timer-outline', 'color' => 'bg-label-primary'];
        $fmt = fn ($seconds) => \App\Helpers\GlobalHelper::formatSeconds((int) $seconds);
        $clock = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('h:i:s A') : null;
        $shortName = fn ($name) => trim(explode(' -', $name)[0]);
    @endphp
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y profile-page">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <h4 class="py-3 mb-4"><span class="text-muted fw-light">Attendance /</span> Daily Breaks</h4>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-primary mb-3 mb-sm-0 no-print">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Attendance
                </a>
            </div>

            <!-- Filters -->
            <div class="card mb-4 no-print">
                <div class="card-body">
                    <form method="GET" action="{{ route('attendance.detail') }}" id="breaksFilterForm" class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label" for="breaksUser">User</label>
                            <select id="breaksUser" name="user" class="select2 form-select">
                                <option value="">Select a user</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ $target && $target->id === $u->id ? 'selected' : '' }}>
                                        {{ $shortName($u->name) }}{{ $u->status ? '' : ' (inactive)' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="breaksDate">Shift date</label>
                            <div class="input-group">
                                <a class="btn btn-outline-secondary {{ $target ? '' : 'disabled' }}" title="Previous day"
                                    href="{{ route('attendance.detail', array_filter(['user' => $target?->id, 'date' => $dateCarbon->copy()->subDay()->format('Y-m-d')])) }}">
                                    <i class="mdi mdi-chevron-left"></i>
                                </a>
                                <input type="date" id="breaksDate" name="date" class="form-control" value="{{ $date }}"
                                    max="{{ now('Asia/Karachi')->format('Y-m-d') }}">
                                <a class="btn btn-outline-secondary {{ $target ? '' : 'disabled' }}" title="Next day"
                                    href="{{ route('attendance.detail', array_filter(['user' => $target?->id, 'date' => $dateCarbon->copy()->addDay()->format('Y-m-d')])) }}">
                                    <i class="mdi mdi-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">Show breaks</button>
                            @if($target)
                                <button type="button" class="btn btn-outline-secondary" onclick="window.print()" title="Print">
                                    <i class="mdi mdi-printer-outline"></i>
                                </button>
                            @endif
                        </div>
                    </form>
                    <div class="text-muted small mt-2">
                        The night shift runs 7&nbsp;PM &rarr; 4&nbsp;AM, so breaks taken after midnight appear under the date the shift started.
                    </div>
                </div>
            </div>

            @if(!$target)
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        <i class="mdi mdi-coffee-outline mdi-48px d-block mb-2"></i>
                        Choose a user and a date to see the breaks they took.
                    </div>
                </div>
            @else
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                    <h5 class="mb-0">
                        {{ $shortName($target->name) }}
                        <small class="text-muted fw-normal">&mdash; {{ $dateCarbon->format('l, d M Y') }}</small>
                    </h5>
                    @if($attendance)
                        <span class="badge bg-label-primary">
                            Clocked in {{ \Carbon\Carbon::parse($attendance->login_time)->format('h:i A') }}
                            @if($attendance->logout_time) &middot; out {{ \Carbon\Carbon::parse($attendance->logout_time)->format('h:i A') }} @endif
                        </span>
                    @endif
                </div>

                @if(!$attendance)
                    <div class="alert alert-secondary">
                        No attendance record for {{ $shortName($target->name) }} on this date (absent, weekend, or not clocked in).
                    </div>
                @elseif($breaks->isEmpty())
                    <div class="alert alert-info">
                        {{ $shortName($target->name) }} was clocked in but did not take any breaks on this date.
                    </div>
                @else
                    <div class="row g-4 mb-4">
                        @php
                            $cards = [
                                ['icon' => 'mdi-timer-outline', 'label' => 'Total Break Time', 'value' => $fmt($stats['total_seconds']), 'color' => 'bg-label-primary'],
                                ['icon' => 'mdi-coffee-outline', 'label' => 'Breaks Taken', 'value' => $stats['count'], 'color' => 'bg-label-info'],
                                ['icon' => 'mdi-timer-sand', 'label' => 'Longest Break', 'value' => $fmt($stats['longest_seconds']), 'color' => 'bg-label-warning'],
                                ['icon' => 'mdi-chart-line', 'label' => 'Average Break', 'value' => $fmt($stats['average_seconds']), 'color' => 'bg-label-success'],
                            ];
                        @endphp
                        @foreach($cards as $card)
                            @include('pages.profile.partials.stat-card', $card)
                        @endforeach
                    </div>

                    @if($stats['by_type']->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @foreach($stats['by_type'] as $key => $row)
                                @php $type = $typeOf($key); @endphp
                                <span class="badge {{ $type['color'] }} px-3 py-2">
                                    <i class="mdi {{ $type['icon'] }} me-1"></i>{{ $type['label'] }}
                                    &middot; {{ $row['count'] }} &times; &middot; {{ $fmt($row['seconds']) }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    @if($stats['open'] || $stats['invalid'])
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert-outline me-1"></i>
                            @if($stats['open']) {{ $stats['open'] }} break(s) were never ended. @endif
                            @if($stats['invalid']) {{ $stats['invalid'] }} break(s) were saved with an invalid (zero/negative) duration. @endif
                            These are shown below but left out of the totals.
                        </div>
                    @endif

                    <div class="card mb-4">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Started</th>
                                        <th>Ended</th>
                                        <th class="text-end">Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($breaks as $i => $break)
                                        @php
                                            $type = $typeOf($break->break_type);
                                            $open = !$break->break_end;
                                            $bad = $break->break_end && $break->duration <= 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td><span class="badge {{ $type['color'] }}"><i class="mdi {{ $type['icon'] }} me-1"></i>{{ $type['label'] }}</span></td>
                                            <td>{{ $clock($break->break_start) }}</td>
                                            <td>
                                                @if($open)
                                                    <span class="badge bg-label-warning">Not ended</span>
                                                @else
                                                    {{ $clock($break->break_end) }}
                                                @endif
                                            </td>
                                            <td class="text-end fw-semibold">
                                                @if($open)
                                                    &mdash;
                                                @elseif($bad)
                                                    <span class="badge bg-label-danger" title="Saved duration: {{ $break->duration }}s">Invalid</span>
                                                @else
                                                    {{ $fmt($break->duration) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <th colspan="4" class="text-end">Total break time</th>
                                        <th class="text-end">{{ $fmt($stats['total_seconds']) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif
            @endif
        </div>
        <div class="content-backdrop fade"></div>
    </div>
@endsection
@section('js')
    <script>
        // Re-run as soon as a user or date is picked (Select2 fires a normal change event).
        $('#breaksUser, #breaksDate').on('change', function () {
            document.getElementById('breaksFilterForm').submit();
        });
    </script>
@endsection
