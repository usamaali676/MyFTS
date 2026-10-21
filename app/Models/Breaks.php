<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Breaks extends Model
{
    // No real break is longer than this; anything over it is a break that
    // was left open (e.g. ended the next morning) and shouldn't count.
    public const MAX_PLAUSIBLE_SECONDS = 4 * 3600;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'break_start',
        'break_end',
        'break_type',
        'duration',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * The real length of the break in seconds, worked out from the two
     * clock times and wrapping past midnight (the night shift runs
     * 7 PM -> 4 AM, so 11:20 PM -> 12:01 AM is 41 minutes, not 23 hours).
     *
     * The saved `duration` column can't be trusted for older rows: the old
     * code stamped the wrong calendar date on the break's end, so it stored
     * a negative or ~24-hour value for breaks that crossed midnight. The
     * calendar dates on those rows are unreliable too, so only the times of
     * day are used. Null while the break is still open.
     */
    public function getEffectiveDurationAttribute(): ?int
    {
        if (!$this->break_start || !$this->break_end) {
            return null;
        }

        $start = Carbon::parse($this->break_start);
        $end = Carbon::parse($this->break_end);

        // The date mix-up only ever shifted the end by a day. If the two
        // dates are further apart than that, the break was genuinely left
        // open for days — report the real elapsed time (which is then
        // flagged as implausible) instead of "wrapping" it into a normal
        // looking number.
        if (abs((int) $start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay())) > 1) {
            return abs($end->getTimestamp() - $start->getTimestamp());
        }

        return ($end->secondsSinceMidnight() - $start->secondsSinceMidnight() + 86400) % 86400;
    }

    /** A finished break whose length is believable (counts toward totals). */
    public function getIsCountableAttribute(): bool
    {
        $seconds = $this->effective_duration;

        return $seconds !== null && $seconds > 0 && $seconds <= self::MAX_PLAUSIBLE_SECONDS;
    }
}
