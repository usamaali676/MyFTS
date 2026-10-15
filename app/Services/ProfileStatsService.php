<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Breaks;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\LeadCloser;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleCS;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Builds the data shown on a user's Profile page: attendance/break stats,
 * and whichever sales-attribution sections actually apply to that user
 * (Closer / Customer Support / Sales rep / Manager CS team), falling back
 * to a company-wide overview when none of those apply (e.g. Admin,
 * Creator, QA, Accounts with no personal sales link).
 *
 * Roles in this app are open-ended (see TeamAttendanceOverviewService), so
 * sections are driven by whether the target user actually has the
 * underlying records, not by a hardcoded role-name switch.
 */
class ProfileStatsService
{
    public const RANGE_ALL = DateRangeResolver::RANGE_ALL;
    public const RANGE_THIS_MONTH = DateRangeResolver::RANGE_THIS_MONTH;
    public const RANGE_LAST_MONTH = DateRangeResolver::RANGE_LAST_MONTH;
    public const RANGE_CUSTOM = DateRangeResolver::RANGE_CUSTOM;

    // A role with a personal sales-attribution section of its own — these
    // users always see their own section (even all-zero, if the selected
    // range has no activity), never the company-wide fallback below.
    private const OPERATIONAL_ROLES = ['TSR', ...Role::CLOSER_ROLES, 'Customer Support', 'Manager Customer Support'];

    // Same set SaleVisibilityService treats as full access to Sales
    // elsewhere in the app — Company Overview (company-wide revenue) is
    // gated the same way, so it doesn't show figures on a Profile page
    // that role couldn't already see via the Sales list.
    private const FULL_ACCESS_ROLES = ['Creator', 'QA', 'Executives', 'Accounts'];

    public function build(User $target, string $rangeKey, ?string $customFrom, ?string $customTo): array
    {
        $range = DateRangeResolver::resolve($rangeKey, $customFrom, $customTo);
        $from = $range['from'];
        $to = $range['to'];

        $roleName = optional($target->role)->name;
        $isOperational = in_array($roleName, self::OPERATIONAL_ROLES, true);
        $hasFullAccess = (int) $target->role_id === 1 || in_array($roleName, self::FULL_ACCESS_ROLES, true);

        $closer = $this->closerStats($target, $from, $to, $isOperational && in_array($roleName, Role::CLOSER_ROLES, true));
        $customerSupport = $this->customerSupportStats(
            $target, $from, $to,
            $isOperational && in_array($roleName, ['Customer Support', 'Manager Customer Support'], true)
        );
        $salesRep = $this->salesRepStats($target, $from, $to, $isOperational && $roleName === 'TSR');
        $team = $this->teamStats($target, $from, $to);

        $company = null;
        if (!$closer && !$customerSupport && !$salesRep && !$team && $hasFullAccess) {
            $company = $this->companyStats($from, $to);
        }

        return [
            'range' => $range,
            'attendance' => $this->attendanceStats($target, $from, $to),
            'closer' => $closer,
            'customerSupport' => $customerSupport,
            'salesRep' => $salesRep,
            'team' => $team,
            'company' => $company,
        ];
    }

    private function scopeDate(Builder $query, string $column, ?Carbon $from, ?Carbon $to, bool $dateOnly = false): void
    {
        if ($from) {
            $query->where($column, '>=', $dateOnly ? $from->format('Y-m-d') : $from);
        }
        if ($to) {
            $query->where($column, '<=', $dateOnly ? $to->format('Y-m-d') : $to);
        }
    }

    /**
     * A Sale query constrained to only sales that qualify as active — same
     * definition as SaleVisibilityService, restated here because that
     * service's qualifying() operates on a Lead query, not a Sale query.
     */
    private function qualifyingSales(Builder $saleQuery): Builder
    {
        return $saleQuery->where(function (Builder $q) {
            $q->where('status', 1)->orWhereHas('invoice.payments');
        });
    }

    // --- Attendance / breaks -------------------------------------------------

    private function attendanceStats(User $user, ?Carbon $from, ?Carbon $to): ?array
    {
        $query = Attendance::where('user_id', $user->id);
        $this->scopeDate($query, 'shift_date', $from, $to, true);

        $shifts = (clone $query)->count();
        if ($shifts === 0) {
            return null;
        }

        $lates = (clone $query)->where('is_late', true)->count();
        $absents = $this->absentDays($user, $from, $to);

        $attendanceIds = (clone $query)->pluck('id');
        // duration > 0 (not just whereNotNull): a handful of rows have a
        // break_end recorded a calendar day before break_start (an
        // overnight-shift data-entry bug), producing a large negative
        // duration that would otherwise swamp the average/total for any
        // user whose range only has a few real breaks.
        $breaks = Breaks::whereIn('attendance_id', $attendanceIds)->where('duration', '>', 0)->get();
        $breakCount = $breaks->count();
        $totalBreakSeconds = (int) $breaks->sum('duration');
        // "Avg Break Duration" means per day (all breaks taken across a
        // shift, added up, then averaged over the days that had any) — not
        // the average length of a single break session, which reads much
        // shorter since most days take several short breaks.
        $daysWithBreaks = $breaks->pluck('attendance_id')->unique()->count();
        $avgBreakSecondsPerDay = $daysWithBreaks > 0 ? (int) round($totalBreakSeconds / $daysWithBreaks) : 0;

        return [
            'shifts' => $shifts,
            'lates' => $lates,
            'absents' => $absents,
            'break_count' => $breakCount,
            'total_break_seconds' => $totalBreakSeconds,
            'avg_break_seconds_per_day' => $avgBreakSecondsPerDay,
            'avg_breaks_per_shift' => $shifts > 0 ? round($breakCount / $shifts, 1) : 0,
        ];
    }

    /**
     * Weekdays with no attendance punch at all, in [start, end) — same rule
     * used on the Home dashboard and Attendance calendar, generalized to an
     * arbitrary range. Never counts today (attendance may not be in yet) or
     * any day before the user's account existed.
     */
    private function absentDays(User $user, ?Carbon $from, ?Carbon $to): int
    {
        $tz = config('app.shift_timezone');
        $createdAt = Carbon::parse($user->created_at, $tz)->startOfDay();
        $today = now($tz)->startOfDay();

        $start = $from ? $from->copy()->startOfDay() : $createdAt->copy();
        if ($start->lt($createdAt)) {
            $start = $createdAt->copy();
        }

        $endBoundary = $to ? $to->copy()->startOfDay() : $today->copy();
        // Exclusive upper bound: the day after $endBoundary when it's fully
        // in the past, or today itself when the range runs into the present.
        $exclusiveEnd = $endBoundary->lt($today) ? $endBoundary->copy()->addDay() : $today->copy();

        if ($start->gte($exclusiveEnd)) {
            return 0;
        }

        $markedDates = Attendance::where('user_id', $user->id)
            ->whereBetween('shift_date', [$start->format('Y-m-d'), $exclusiveEnd->copy()->subDay()->format('Y-m-d')])
            ->pluck('shift_date')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
            ->flip();

        $absents = 0;
        $cursor = $start->copy();
        while ($cursor->lt($exclusiveEnd)) {
            if (!$cursor->isWeekend() && !$markedDates->has($cursor->format('Y-m-d'))) {
                $absents++;
            }
            $cursor->addDay();
        }

        return $absents;
    }

    // --- Closer ---------------------------------------------------------------

    private function closerRevenue(User $user, ?Carbon $from, ?Carbon $to): array
    {
        $leadIdsQuery = LeadCloser::where('closer_id', $user->id);
        $this->scopeDate($leadIdsQuery, 'created_at', $from, $to);
        $leadIds = $leadIdsQuery->pluck('lead_id')->unique();

        if ($leadIds->isEmpty()) {
            return [0, 0.0];
        }

        $saleIds = $this->qualifyingSales(Sale::whereIn('lead_id', $leadIds))->pluck('id');
        if ($saleIds->isEmpty()) {
            return [0, 0.0];
        }

        $revenue = (float) Invoice::whereIn('sale_id', $saleIds)->whereHas('payments')->sum('total_amount');

        return [$saleIds->count(), $revenue];
    }

    private function closerStats(User $user, ?Carbon $from, ?Carbon $to, bool $forceShow = false): ?array
    {
        [$count, $revenue] = $this->closerRevenue($user, $from, $to);
        if ($count === 0 && !$forceShow) {
            return null;
        }

        return [
            'sales' => $count,
            'revenue' => $revenue,
            'avg_deal' => $count > 0 ? round($revenue / $count, 2) : 0,
            'rank' => $this->rankAmongPeers($user, $from, $to, fn ($peer, $f, $t) => $this->closerRevenue($peer, $f, $t)),
        ];
    }

    // --- Customer Support -------------------------------------------------------

    private function customerSupportRevenue(User $user, ?Carbon $from, ?Carbon $to): array
    {
        $saleIdsQuery = SaleCS::where('cs_id', $user->id);
        $this->scopeDate($saleIdsQuery, 'created_at', $from, $to);
        $saleIds = $saleIdsQuery->pluck('sale_id')->unique();

        if ($saleIds->isEmpty()) {
            return [0, 0.0];
        }

        $qualifyingSaleIds = $this->qualifyingSales(Sale::whereIn('id', $saleIds))->pluck('id');
        if ($qualifyingSaleIds->isEmpty()) {
            return [0, 0.0];
        }

        $revenue = (float) Invoice::whereIn('sale_id', $qualifyingSaleIds)->whereHas('payments')->sum('total_amount');

        return [$qualifyingSaleIds->count(), $revenue];
    }

    private function customerSupportStats(User $user, ?Carbon $from, ?Carbon $to, bool $forceShow = false): ?array
    {
        [$count, $revenue] = $this->customerSupportRevenue($user, $from, $to);
        if ($count === 0 && !$forceShow) {
            return null;
        }

        return [
            'sales' => $count,
            'revenue' => $revenue,
            'avg_deal' => $count > 0 ? round($revenue / $count, 2) : 0,
            'rank' => $this->rankAmongPeers($user, $from, $to, fn ($peer, $f, $t) => $this->customerSupportRevenue($peer, $f, $t)),
        ];
    }

    // --- Sales rep / lead-gen (the "saler" on a Lead) -----------------------

    private function salesRepRevenue(User $user, ?Carbon $from, ?Carbon $to): array
    {
        $leadsQuery = Lead::where('saler_id', $user->id);
        $this->scopeDate($leadsQuery, 'created_at', $from, $to);
        $leadIds = $leadsQuery->pluck('id');

        if ($leadIds->isEmpty()) {
            return [0, 0.0];
        }

        $saleIds = $this->qualifyingSales(Sale::whereIn('lead_id', $leadIds))->pluck('id');
        if ($saleIds->isEmpty()) {
            return [0, 0.0];
        }

        $revenue = (float) Invoice::whereIn('sale_id', $saleIds)->whereHas('payments')->sum('total_amount');

        return [$saleIds->count(), $revenue];
    }

    private function salesRepStats(User $user, ?Carbon $from, ?Carbon $to, bool $forceShow = false): ?array
    {
        $leadsQuery = Lead::where('saler_id', $user->id);
        $this->scopeDate($leadsQuery, 'created_at', $from, $to);
        $leadsCount = $leadsQuery->count();

        if ($leadsCount === 0 && !$forceShow) {
            return null;
        }

        [$convertedCount, $revenue] = $this->salesRepRevenue($user, $from, $to);

        return [
            'leads' => $leadsCount,
            'converted' => $convertedCount,
            'conversion_rate' => $leadsCount > 0 ? round($convertedCount / $leadsCount * 100, 1) : 0,
            'revenue' => $revenue,
            'rank' => $this->rankAmongPeers($user, $from, $to, fn ($peer, $f, $t) => $this->salesRepRevenue($peer, $f, $t)),
        ];
    }

    // --- Manager Customer Support team overview --------------------------------

    private function teamStats(User $user, ?Carbon $from, ?Carbon $to): ?array
    {
        if (optional($user->role)->name !== 'Manager Customer Support') {
            return null;
        }

        $csRoleId = Role::where('name', 'Customer Support')->value('id');
        $csUsers = $csRoleId ? User::where('role_id', $csRoleId)->where('status', 1)->get() : collect();

        $totalSales = 0;
        $totalRevenue = 0.0;
        $reps = collect();

        foreach ($csUsers as $cs) {
            [$count, $revenue] = $this->customerSupportRevenue($cs, $from, $to);
            $totalSales += $count;
            $totalRevenue += $revenue;
            if ($count > 0) {
                $reps->push(['user' => $cs, 'sales' => $count, 'revenue' => $revenue]);
            }
        }

        return [
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'reps' => $reps->sortByDesc('revenue')->values(),
        ];
    }

    // --- Company-wide fallback ---------------------------------------------

    private function companyStats(?Carbon $from, ?Carbon $to): array
    {
        $salesQuery = $this->qualifyingSales(Sale::query());
        $this->scopeDate($salesQuery, 'created_at', $from, $to);
        $saleIds = $salesQuery->pluck('id');

        $revenue = (float) Invoice::whereIn('sale_id', $saleIds)->whereHas('payments')->sum('total_amount');

        $operationalRoles = ['TSR', ...Role::CLOSER_ROLES, 'Customer Support'];
        $roleCounts = User::where('status', 1)
            ->whereHas('role', fn (Builder $q) => $q->whereIn('name', $operationalRoles))
            ->with('role')
            ->get()
            ->groupBy(fn (User $u) => $u->role->name)
            ->map->count();

        return [
            'total_sales' => $saleIds->count(),
            'total_revenue' => $revenue,
            'tsr_count' => $roleCounts->get('TSR', 0),
            'closer_count' => collect(Role::CLOSER_ROLES)->sum(fn ($name) => $roleCounts->get($name, 0)),
            'csr_count' => $roleCounts->get('Customer Support', 0),
        ];
    }

    // --- Shared rank helper --------------------------------------------------

    /**
     * @param callable(User, ?Carbon, ?Carbon): array{0:int,1:float} $metricFn
     */
    private function rankAmongPeers(User $user, ?Carbon $from, ?Carbon $to, callable $metricFn): array
    {
        $peers = User::where('role_id', $user->role_id)->where('status', 1)->get();

        $ranked = $peers->map(function (User $peer) use ($from, $to, $metricFn) {
                [$count, $revenue] = $metricFn($peer, $from, $to);
                return ['user_id' => $peer->id, 'sales' => $count, 'revenue' => $revenue];
            })
            ->filter(fn ($row) => $row['sales'] > 0)
            ->sortByDesc('revenue')
            ->values();

        $position = $ranked->search(fn ($row) => $row['user_id'] === $user->id);

        return [
            'position' => $position === false ? null : $position + 1,
            'of' => $ranked->count(),
        ];
    }
}
