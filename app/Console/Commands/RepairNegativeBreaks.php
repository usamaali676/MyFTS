<?php

namespace App\Console\Commands;

use App\Models\Breaks;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairNegativeBreaks extends Command
{
    protected $signature = 'breaks:repair-negative {--apply : Actually write the fixes (without this it is a dry run)}';

    protected $description = 'Fix breaks saved with a negative duration (break_end stamped on the wrong calendar day)';

    // A break longer than this isn't plausible, so such a row is left alone
    // for a human to look at rather than "repaired" into a wrong number.
    private const MAX_PLAUSIBLE_SECONDS = 4 * 3600;

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        $rows = Breaks::where('duration', '<', 0)->whereNotNull('break_end')->orderBy('id')->get();

        if ($rows->isEmpty()) {
            $this->info('No breaks with a negative duration. Nothing to do.');

            return self::SUCCESS;
        }

        $fixable = [];
        $skipped = [];

        foreach ($rows as $break) {
            $start = Carbon::parse($break->break_start);
            $end = Carbon::parse($break->break_end);

            // The end has the right time of day but the wrong date: put it on
            // the start's date, and roll to the next day if that lands before
            // the start (the break crossed midnight).
            $fixedEnd = $start->copy()->setTimeFrom($end);
            if ($fixedEnd->lt($start)) {
                $fixedEnd->addDay();
            }

            $seconds = $fixedEnd->getTimestamp() - $start->getTimestamp();

            if ($seconds > 0 && $seconds <= self::MAX_PLAUSIBLE_SECONDS) {
                $fixable[] = [$break, $fixedEnd, $seconds];
            } else {
                $skipped[] = $break;
            }
        }

        $this->table(
            ['id', 'user', 'break_start', 'old break_end', 'old duration', 'new break_end', 'new duration'],
            collect($fixable)->map(fn ($f) => [
                $f[0]->id, $f[0]->user_id, $f[0]->break_start, $f[0]->break_end, $f[0]->duration,
                $f[1]->format('Y-m-d H:i:s'), $f[2] . 's (' . round($f[2] / 60, 1) . ' min)',
            ])->all()
        );

        foreach ($skipped as $break) {
            $this->warn("Skipped #{$break->id}: not a plausible break after correction, check it by hand.");
        }

        if (!$apply) {
            $this->newLine();
            $this->info(count($fixable) . ' row(s) would be fixed. This was a dry run; re-run with --apply to write them.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($fixable) {
            foreach ($fixable as [$break, $fixedEnd, $seconds]) {
                $break->break_end = $fixedEnd;
                $break->duration = $seconds;
                $break->save();
            }
        });

        $this->info(count($fixable) . ' row(s) fixed.');

        return self::SUCCESS;
    }
}
