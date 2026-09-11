<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Decides which leads count as "Sales" (split out of the Leads list) and
 * who is allowed to see which of them.
 *
 *  - A lead qualifies as a Sale once its sale is active (status == 1) or has
 *    ever had a payment charged against it (Sale -> Invoice -> Payment),
 *    even if status was later flipped off (e.g. after a chargeback).
 *  - Admin, Creator, QA, Executives, Accounts: every qualifying sale.
 *  - Manager Customer Support: their own sales (where they're the assigned
 *    CS rep) via the regular Sales view — plus a separate Team Sales view
 *    covering the whole CS team, optionally narrowed to one rep.
 *  - Customer Support: only sales where they're the assigned CS rep.
 *  - Closer: only sales where they're a closer on the underlying lead.
 *  - Anyone else: no access at all.
 */
class SaleVisibilityService
{
    private const FULL_ACCESS_ROLES = ['Creator', 'QA', 'Executives', 'Accounts'];

    public static function canAccess(User $user): bool
    {
        if ((int) $user->role_id === 1) {
            return true;
        }

        $roleName = optional($user->role)->name;

        return in_array($roleName, self::FULL_ACCESS_ROLES, true)
            || in_array($roleName, ['Manager Customer Support', 'Customer Support', 'Closer'], true);
    }

    public static function canAccessTeam(User $user): bool
    {
        return (int) $user->role_id === 1 || optional($user->role)->name === 'Manager Customer Support';
    }

    public static function isManagerCustomerSupport(User $user): bool
    {
        return (int) $user->role_id !== 1 && optional($user->role)->name === 'Manager Customer Support';
    }

    /**
     * Constrains a Lead query to only the leads that qualify as a Sale.
     */
    public static function qualifying(Builder $query): Builder
    {
        return $query->whereHas('sale', function (Builder $q) {
            $q->where('status', 1)->orWhereHas('invoice.payments');
        });
    }

    public static function forViewer(User $user): Builder
    {
        $query = self::qualifying(
            Lead::with(['sale.Customer_support.user', 'closers.user', 'saler', 'chargeback'])
        )->orderByDesc('id');

        if ((int) $user->role_id === 1 || in_array(optional($user->role)->name, self::FULL_ACCESS_ROLES, true)) {
            return $query;
        }

        $roleName = optional($user->role)->name;

        if (in_array($roleName, ['Customer Support', 'Manager Customer Support'], true)) {
            return $query->whereHas('sale.Customer_support', function (Builder $q) use ($user) {
                $q->where('cs_id', $user->id);
            });
        }

        if ($roleName === 'Closer') {
            return $query->whereHas('closers', function (Builder $q) use ($user) {
                $q->where('closer_id', $user->id);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * Team-wide sales for the whole Customer Support team — Manager
     * Customer Support's separate "Team Sales" view. Admin can reach it too
     * (same convention as everywhere else), optionally narrowed to one rep.
     */
    public static function forTeam(?int $filterCsId = null): Builder
    {
        $csRoleId = Role::where('name', 'Customer Support')->value('id');

        $query = self::qualifying(
            Lead::with(['sale.Customer_support.user', 'closers.user', 'saler', 'chargeback'])
        )
            ->whereHas('sale.Customer_support.user', function (Builder $q) use ($csRoleId) {
                $q->where('role_id', $csRoleId);
            })
            ->orderByDesc('id');

        if ($filterCsId) {
            $query->whereHas('sale.Customer_support', function (Builder $q) use ($filterCsId) {
                $q->where('cs_id', $filterCsId);
            });
        }

        return $query;
    }
}
