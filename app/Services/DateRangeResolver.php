<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Resolves the shared "All Time / This Month / Last Month / Custom Range"
 * filter used on both the Profile page and the Reports dashboard, so
 * "This Month" means the same thing everywhere in the app.
 */
class DateRangeResolver
{
    public const RANGE_ALL = 'all';
    public const RANGE_THIS_MONTH = 'this_month';
    public const RANGE_LAST_MONTH = 'last_month';
    public const RANGE_CUSTOM = 'custom';

    /**
     * @return array{key: string, label: string, from: ?Carbon, to: ?Carbon}
     */
    public static function resolve(string $key, ?string $customFrom, ?string $customTo): array
    {
        $tz = config('app.shift_timezone');

        if ($key === self::RANGE_THIS_MONTH) {
            return [
                'key' => $key, 'label' => 'This Month',
                'from' => now($tz)->startOfMonth(), 'to' => now($tz)->endOfMonth(),
            ];
        }

        if ($key === self::RANGE_LAST_MONTH) {
            $lastMonth = now($tz)->subMonthNoOverflow();
            return [
                'key' => $key, 'label' => 'Last Month',
                'from' => $lastMonth->copy()->startOfMonth(), 'to' => $lastMonth->copy()->endOfMonth(),
            ];
        }

        if ($key === self::RANGE_CUSTOM && ($customFrom || $customTo)) {
            return [
                'key' => $key, 'label' => 'Custom Range',
                'from' => $customFrom ? Carbon::parse($customFrom, $tz)->startOfDay() : null,
                'to' => $customTo ? Carbon::parse($customTo, $tz)->endOfDay() : null,
            ];
        }

        return ['key' => self::RANGE_ALL, 'label' => 'All Time', 'from' => null, 'to' => null];
    }
}
