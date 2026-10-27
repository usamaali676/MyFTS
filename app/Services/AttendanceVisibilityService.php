<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Whose attendance and breaks a viewer may see in the Attendance module
 * (list, calendar, Daily Breaks, and the list's filter endpoint).
 *
 * Everyone who has the Attendance permission keeps seeing everyone — that is
 * unchanged. The one scoped role is the Closer - Sales Manager: they get the
 * module, but only for the teams they oversee (TSR, Closer, Customer Support
 * and QA), never other departments, other managers, or themselves.
 */
class AttendanceVisibilityService
{
    private const SALES_MANAGER_VISIBLE_ROLES = ['TSR', Role::CLOSER, 'Customer Support', 'QA'];

    public static function isScoped(User $viewer): bool
    {
        return optional($viewer->role)->name === Role::CLOSER_SALES_MANAGER;
    }

    /**
     * The user ids this viewer may see, or null when they may see everyone.
     *
     * @return Collection<int, int>|null
     */
    public static function visibleUserIds(User $viewer): ?Collection
    {
        if (!self::isScoped($viewer)) {
            return null;
        }

        $roleIds = Role::whereIn('name', self::SALES_MANAGER_VISIBLE_ROLES)->pluck('id');

        return User::whereIn('role_id', $roleIds)->pluck('id');
    }
}
