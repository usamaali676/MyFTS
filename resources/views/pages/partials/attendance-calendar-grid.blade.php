{{-- Expects: $weeks, $records, $month, $joinedAt --}}
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
            @php $today = now('Asia/Karachi')->startOfDay(); @endphp
            @foreach($weeks as $week)
                <tr>
                    @foreach($week as $day)
                        @php
                            $key = $day->format('Y-m-d');
                            $inMonth = $day->month === $month->month;
                            $record = $records->get($key);
                            $isToday = $day->isToday();

                            $cellClass = 'text-muted';
                            $label = null;
                            if ($inMonth && $record) {
                                if ($record->is_late) {
                                    $cellClass = 'bg-label-warning';
                                    $label = 'Late';
                                } else {
                                    $cellClass = 'bg-label-success';
                                    $label = 'On-Time';
                                }
                            } elseif ($inMonth && $day->isWeekend()) {
                                $cellClass = 'bg-label-secondary';
                                $label = 'Weekend';
                            } elseif ($inMonth && $joinedAt && $day->lt($joinedAt)) {
                                // Not yet a user on this day — not absent, just doesn't apply.
                                $cellClass = 'text-muted bg-light';
                            } elseif ($inMonth && $day->lt($today)) {
                                $cellClass = 'bg-label-danger';
                                $label = 'Absent';
                            } elseif (!$inMonth) {
                                $cellClass = 'text-muted bg-light';
                            }
                        @endphp
                        <td class="{{ $cellClass }}" style="height: 70px; vertical-align: top; {{ $isToday ? 'outline: 2px solid var(--bs-primary); outline-offset: -2px;' : '' }}">
                            <div class="fw-semibold">{{ $day->day }}</div>
                            @if($label)
                                <small class="d-block">{{ $label }}</small>
                            @endif
                            @if($inMonth && $record && $record->half_day)
                                <small class="d-block"><span class="badge bg-info">Half Day</span></small>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
