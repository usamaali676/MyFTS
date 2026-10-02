@extends('layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/profile-enhance.css') }}" />
@endsection
@section('content')
    @php
        $initials = collect(explode(' ', trim($target->name)))
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('');
        $initials = strtoupper($initials) ?: '?';

        $rankLabel = function (?array $rank) {
            if (!$rank || !$rank['position']) {
                return 'Not ranked this period';
            }
            return "#{$rank['position']} of {$rank['of']}";
        };
    @endphp
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y profile-page">
            <h4 class="py-3 mb-4">
                <span class="text-muted fw-light">Profile /</span> {{ $isSelf ? 'My Profile' : $target->name }}
            </h4>

            @unless($isSelf)
                <div class="alert alert-info d-flex align-items-center mb-4">
                    <i class="mdi mdi-eye-outline me-2"></i>
                    <span>Viewing <strong>{{ $target->name }}</strong>'s profile.</span>
                    <a href="{{ route('user.index') }}" class="ms-auto">Back to Users</a>
                </div>
            @endunless

            <!-- Identity card -->
            <div class="card mb-4">
                <div class="card-body d-flex flex-wrap align-items-center profile-identity">
                    <div class="avatar avatar-xl me-4 mb-2 mb-sm-0">
                        <span class="avatar-initial rounded-circle bg-label-primary profile-initials">{{ $initials }}</span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">{{ $target->name }}</h5>
                        <p class="text-muted mb-2">{{ $target->email }}</p>
                        <span class="badge bg-label-primary me-2">{{ optional($target->role)->name ?? 'N/A' }}</span>
                        <span class="badge {{ $target->status ? 'bg-label-success' : 'bg-label-secondary' }} me-2">
                            {{ $target->status ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="text-muted small">Joined {{ \Carbon\Carbon::parse($target->created_at)->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Range filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('profile.show', $isSelf ? null : $target->id) }}"
                        class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Time Range</label>
                            <select name="range" id="profile_range_select" class="form-select">
                                <option value="all" {{ $rangeKey == 'all' ? 'selected' : '' }}>All Time</option>
                                <option value="this_month" {{ $rangeKey == 'this_month' ? 'selected' : '' }}>This Month</option>
                                <option value="last_month" {{ $rangeKey == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                <option value="custom" {{ $rangeKey == 'custom' ? 'selected' : '' }}>Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3 profile-custom-range-field" {{ $rangeKey == 'custom' ? '' : 'style=display:none' }}>
                            <label class="form-label">From</label>
                            <input type="date" name="from" class="form-control" value="{{ $customFrom }}">
                        </div>
                        <div class="col-md-3 profile-custom-range-field" {{ $rangeKey == 'custom' ? '' : 'style=display:none' }}>
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

            @if($attendance)
                <h6 class="mb-3">Attendance & Breaks</h6>
                <div class="row g-4 mb-4">
                    @php
                        $cards = [
                            ['icon' => 'mdi-calendar-check-outline', 'label' => 'Shifts', 'value' => $attendance['shifts'], 'color' => 'bg-label-primary'],
                            ['icon' => 'mdi-clock-alert-outline', 'label' => 'Late Arrivals', 'value' => $attendance['lates'], 'color' => 'bg-label-warning'],
                            ['icon' => 'mdi-calendar-remove-outline', 'label' => 'Absents', 'value' => $attendance['absents'], 'color' => 'bg-label-danger'],
                            ['icon' => 'mdi-coffee-outline', 'label' => 'Breaks Taken', 'value' => $attendance['break_count'], 'sub' => $attendance['avg_breaks_per_shift'] . ' avg / shift', 'color' => 'bg-label-info'],
                            ['icon' => 'mdi-timer-sand', 'label' => 'Avg Break Duration / Day', 'value' => \App\Helpers\GlobalHelper::formatSeconds($attendance['avg_break_seconds_per_day']), 'color' => 'bg-label-secondary'],
                            ['icon' => 'mdi-timer-outline', 'label' => 'Total Break Time', 'value' => \App\Helpers\GlobalHelper::formatSeconds($attendance['total_break_seconds']), 'color' => 'bg-label-secondary'],
                        ];
                    @endphp
                    @foreach($cards as $card)
                        @include('pages.profile.partials.stat-card', $card)
                    @endforeach
                </div>
            @endif

            @if($closer)
                <h6 class="mb-3">Closer Performance</h6>
                <div class="row g-4 mb-4">
                    @php
                        $cards = [
                            ['icon' => 'mdi-handshake-outline', 'label' => 'Sales Closed', 'value' => $closer['sales'], 'color' => 'bg-label-primary'],
                            ['icon' => 'mdi-currency-usd', 'label' => 'Revenue', 'value' => '$' . number_format($closer['revenue'], 2), 'color' => 'bg-label-success'],
                            ['icon' => 'mdi-chart-line', 'label' => 'Avg Deal Size', 'value' => '$' . number_format($closer['avg_deal'], 2), 'color' => 'bg-label-info'],
                            ['icon' => 'mdi-trophy-outline', 'label' => 'Rank vs Closers', 'value' => $rankLabel($closer['rank']), 'color' => 'bg-label-warning'],
                        ];
                    @endphp
                    @foreach($cards as $card)
                        @include('pages.profile.partials.stat-card', $card)
                    @endforeach
                </div>
            @endif

            @if($customerSupport)
                <h6 class="mb-3">Customer Support Performance</h6>
                <div class="row g-4 mb-4">
                    @php
                        $cards = [
                            ['icon' => 'mdi-account-heart-outline', 'label' => 'Sales Managed', 'value' => $customerSupport['sales'], 'color' => 'bg-label-primary'],
                            ['icon' => 'mdi-currency-usd', 'label' => 'Revenue', 'value' => '$' . number_format($customerSupport['revenue'], 2), 'color' => 'bg-label-success'],
                            ['icon' => 'mdi-chart-line', 'label' => 'Avg Deal Size', 'value' => '$' . number_format($customerSupport['avg_deal'], 2), 'color' => 'bg-label-info'],
                            ['icon' => 'mdi-trophy-outline', 'label' => 'Rank vs CS Reps', 'value' => $rankLabel($customerSupport['rank']), 'color' => 'bg-label-warning'],
                        ];
                    @endphp
                    @foreach($cards as $card)
                        @include('pages.profile.partials.stat-card', $card)
                    @endforeach
                </div>
            @endif

            @if($salesRep)
                <h6 class="mb-3">Leads & Conversions</h6>
                <div class="row g-4 mb-4">
                    @php
                        $cards = [
                            ['icon' => 'mdi-account-plus-outline', 'label' => 'Leads Created', 'value' => $salesRep['leads'], 'color' => 'bg-label-primary'],
                            ['icon' => 'mdi-handshake-outline', 'label' => 'Converted to Sale', 'value' => $salesRep['converted'], 'sub' => $salesRep['conversion_rate'] . '% conversion', 'color' => 'bg-label-success'],
                            ['icon' => 'mdi-currency-usd', 'label' => 'Revenue', 'value' => '$' . number_format($salesRep['revenue'], 2), 'color' => 'bg-label-info'],
                            ['icon' => 'mdi-trophy-outline', 'label' => 'Rank vs Peers', 'value' => $rankLabel($salesRep['rank']), 'color' => 'bg-label-warning'],
                        ];
                    @endphp
                    @foreach($cards as $card)
                        @include('pages.profile.partials.stat-card', $card)
                    @endforeach
                </div>
            @endif

            @if($team)
                <h6 class="mb-3">Team Overview (Customer Support)</h6>
                <div class="row g-4 mb-4">
                    @php
                        $cards = [
                            ['icon' => 'mdi-account-group-outline', 'label' => 'Team Sales', 'value' => $team['total_sales'], 'color' => 'bg-label-primary'],
                            ['icon' => 'mdi-currency-usd', 'label' => 'Team Revenue', 'value' => '$' . number_format($team['total_revenue'], 2), 'color' => 'bg-label-success'],
                        ];
                    @endphp
                    @foreach($cards as $card)
                        @include('pages.profile.partials.stat-card', $card)
                    @endforeach
                </div>
                @if($team['reps']->isNotEmpty())
                    <div class="card mb-4">
                        <h6 class="card-header">By Rep</h6>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Rep</th>
                                        <th>Sales</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($team['reps'] as $rep)
                                        <tr>
                                            <td>{{ $rep['user']->name }}</td>
                                            <td>{{ $rep['sales'] }}</td>
                                            <td>${{ number_format($rep['revenue'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif

            @if($company)
                <h6 class="mb-3">Company Overview</h6>
                <div class="row g-4 mb-4">
                    @php
                        $cards = [
                            ['icon' => 'mdi-handshake-outline', 'label' => 'Total Sales', 'value' => $company['total_sales'], 'color' => 'bg-label-primary'],
                            ['icon' => 'mdi-currency-usd', 'label' => 'Total Revenue', 'value' => '$' . number_format($company['total_revenue'], 2), 'color' => 'bg-label-success'],
                            ['icon' => 'mdi-account-voice', 'label' => 'Active TSRs', 'value' => $company['tsr_count'], 'color' => 'bg-label-info'],
                            ['icon' => 'mdi-handshake-outline', 'label' => 'Active Closers', 'value' => $company['closer_count'], 'color' => 'bg-label-warning'],
                            ['icon' => 'mdi-account-heart-outline', 'label' => 'Active CSRs', 'value' => $company['csr_count'], 'color' => 'bg-label-secondary'],
                        ];
                    @endphp
                    @foreach($cards as $card)
                        @include('pages.profile.partials.stat-card', $card)
                    @endforeach
                </div>
            @endif

        </div>
        <div class="content-backdrop fade"></div>
    </div>
@endsection
@section('js')
    <script>
        document.getElementById('profile_range_select').addEventListener('change', function () {
            var isCustom = this.value === 'custom';
            document.querySelectorAll('.profile-custom-range-field').forEach(function (el) {
                el.style.display = isCustom ? '' : 'none';
            });
        });
    </script>
@endsection
