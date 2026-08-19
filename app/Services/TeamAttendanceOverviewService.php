<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Builds the "team lates/absents" data shown on the Home dashboard, applying
 * the role-based visibility rules for who is allowed to see whose monthly
 * attendance:
 *
 *  - Creator / Executives: every active user in any role, except roles whose
 *    name contains "IT" (case-insensitive) — this already includes users
 *    #3 and #4, no special-casing needed.
 *  - User #4: every active TSR/Closer, minus IT roles, minus their own record.
 *  - User #3: every active Customer Support user, minus IT roles, minus
 *    their own record.
 *  - Anyone else: no team view.
 *  - Regardless of the above, Creator/Executives/HR Manager role-holders are
 *    never included — this is a leadership/operational-tracking view, not
 *    an org-wide one.
 *
 * To add another rule later, add a branch to policyFor() — everything else
 * (the attendance math, the IT exclusion, self-exclusion) is shared.
 */
class TeamAttendanceOverviewService
{
    private const IT_NEEDLE = 'IT';

    // Roles never tracked in the team lates/absents roll-up, for any viewer —
    // including Creator/Executives looking at "All Departments" (so a
    // Creator doesn't see their own or another Creator/Executive/HR
    // Manager's record mixed into the operational list).
    private const EXCLUDED_ROLES = ['Creator', 'Executives', 'HR Manager', 'Tech Facilities', 'Accounts'];

    /**
     * @return array{
     *     visible: bool,
     *     label: string,
     *     lates: int,
     *     absents: int,
     *     latesByUser: Collection,
     *     absentsByUser: Collection,
     * }
     */
    public function forViewer(User $viewer, Carbon $monthStart, Carbon $today): array
    {
        $policy = $this->policyFor($viewer);

        if (!$policy) {
            return [
                'visible' => false,
                'label' => '',
                'lates' => 0,
                'absents' => 0,
                'latesByUser' => collect(),
                'absentsByUser' => collect(),
            ];
        }

        $teamUsers = $this->visibleUsers($viewer, $policy);

        $monthAttendances = Attendance::whereIn('user_id', $teamUsers->pluck('id'))
            ->whereYear('shift_date', $monthStart->year)
            ->whereMonth('shift_date', $monthStart->month)
            ->get()
            ->groupBy('user_id');

        $lates = 0;
        $absents = 0;
        $latesByUser = collect();
        $absentsByUser = collect();

        foreach ($teamUsers as $teamUser) {
            $userAttendances = $monthAttendances->get($teamUser->id, collect());

            $lateRecords = $userAttendances->where('is_late', true)->sortByDesc('shift_date')->values();
            $lates += $lateRecords->count();
            if ($lateRecords->isNotEmpty()) {
                $latesByUser->push((object) [
                    'user' => $teamUser,
                    'count' => $lateRecords->count(),
                    'records' => $lateRecords,
                ]);
            }

            // Days this month, up to (not including) today, with no punch at
            // all and not a weekend — same "absent" rule used everywhere else
            // (Attendance calendar view, personal Total Absents card). Never
            // count a day before the member's account was even created.
            $markedDates = $userAttendances->pluck('shift_date')
                ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
                ->flip();

            $absentDates = collect();
            $memberCreatedAt = Carbon::parse($teamUser->created_at, 'Asia/Karachi')->startOfDay();
            $cursor = $monthStart->gt($memberCreatedAt) ? $monthStart->copy() : $memberCreatedAt->copy();
            while ($cursor->lt($today)) {
                if (!$cursor->isWeekend() && !$markedDates->has($cursor->format('Y-m-d'))) {
                    $absentDates->push($cursor->copy());
                }
                $cursor->addDay();
            }
            $absents += $absentDates->count();
            if ($absentDates->isNotEmpty()) {
                $absentsByUser->push((object) [
                    'user' => $teamUser,
                    'count' => $absentDates->count(),
                    'dates' => $absentDates->sortByDesc(fn ($d) => $d->format('Y-m-d'))->values(),
                ]);
            }
        }

        return [
            'visible' => true,
            'label' => $policy['label'],
            'lates' => $lates,
            'absents' => $absents,
            'latesByUser' => $latesByUser->sortByDesc('count')->values(),
            'absentsByUser' => $absentsByUser->sortByDesc('count')->values(),
        ];
    }

    /**
     * Who is this viewer allowed to see, and how should the panel be
     * labelled? Returns null if the viewer has no team view at all.
     *
     * @return array{roles: ?array<string>, excludeSelf: bool, excludeUserIds: array<int>, label: string}|null
     */
    private function policyFor(User $viewer): ?array
    {
        $roleName = optional($viewer->role)->name;

        if (in_array($roleName, ['Creator', 'Executives'], true)) {
            // null roles = every non-IT role, which already covers users #3 and #4.
            return ['roles' => null, 'excludeSelf' => false, 'excludeUserIds' => [], 'label' => 'All Departments'];
        }

        if ((int) $viewer->id === 4) {
            return ['roles' => ['TSR', 'Closer'], 'excludeSelf' => true, 'excludeUserIds' => [], 'label' => 'TSR & Closer'];
        }

        if ((int) $viewer->id === 3) {
            // User #18 (Eric Williams, Customer Support) is hidden from user #3's
            // view specifically — Creator/Executives still see them via the
            // "All Departments" branch above, which doesn't use this exclusion.
            return ['roles' => ['Customer Support'], 'excludeSelf' => true, 'excludeUserIds' => [18], 'label' => 'Customer Support'];
        }

        return null;
    }

    /**
     * @param array{roles: ?array<string>, excludeSelf: bool, excludeUserIds: array<int>, label: string} $policy
     */
    private function visibleUsers(User $viewer, array $policy): Collection
    {
        return User::query()
            ->where('status', 1)
            ->with('role')
            ->get()
            ->filter(function (User $candidate) use ($policy) {
                $roleName = optional($candidate->role)->name;

                if (!$roleName || $this->isItRole($roleName) || $this->isExcludedRole($roleName)) {
                    return false;
                }

                return $policy['roles'] === null || in_array($roleName, $policy['roles'], true);
            })
            ->when($policy['excludeSelf'], fn (Collection $users) => $users->reject(
                fn (User $candidate) => (int) $candidate->id === (int) $viewer->id
            ))
            ->when(!empty($policy['excludeUserIds']), fn (Collection $users) => $users->reject(
                fn (User $candidate) => in_array((int) $candidate->id, $policy['excludeUserIds'], true)
            ))
            ->values();
    }

    private function isItRole(string $roleName): bool
    {
        // A plain "contains IT anywhere" substring check also matches roles
        // like "Tech Facilities" (fac-IL-IT-ies) that have nothing to do
        // with IT. Every real IT role in this app is named "IT - <team>"
        // (IT - SEO Manager, IT - Q/A, ...), so a prefix check catches all
        // of those — and still matches "IT", "IT Support", "IT Manager",
        // "Information Technology" — without the false positive.
        return stripos(trim($roleName), self::IT_NEEDLE) === 0;
    }

    private function isExcludedRole(string $roleName): bool
    {
        $roleName = trim($roleName);

        foreach (self::EXCLUDED_ROLES as $excluded) {
            if (strcasecmp($roleName, $excluded) === 0) {
                return true;
            }
        }

        return false;
    }
}
