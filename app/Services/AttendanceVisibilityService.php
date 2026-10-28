<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Whose attendance and breaks a viewer may see in the Attendance module
 * (list, calendar, Daily Breaks, and the list's filter endpoint).
 *
 * Only ACTIVE users (status = 1) ever appear on these screens, for every
 * viewer — dropdowns, table rows and direct links alike. On top of that, the
 * Closer - Sales Manager is limited to the teams they oversee (TSR, Closer,
 * Customer Support and QA), never other departments, other managers, or
 * themselves.
 */
class AttendanceVisibilityService
{
    private const SALES_MANAGER_VISIBLE_ROLES = ['TSR', Role::CLOSER, 'Customer Support', 'QA'];

    public static function isScoped(User $viewer): bool
    {
        return optional($viewer->role)->name === Role::CLOSER_SALES_MANAGER;
    }

    /**
     * The (active) user ids this viewer may see.
     *
     * @return Collection<int, int>
     */
    public static function visibleUserIds(User $viewer): Collection
    {
        $query = User::where('status', 1);

        if (self::isScoped($viewer)) {
            $query->whereIn('role_id', Role::whereIn('name', self::SALES_MANAGER_VISIBLE_ROLES)->pluck('id'));
        }

        return $query->pluck('id');
    }
}
