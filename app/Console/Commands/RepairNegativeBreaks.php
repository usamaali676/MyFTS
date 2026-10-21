<?php

namespace App\Console\Commands;

use App\Models\Breaks;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairNegativeBreaks extends Command
{
    protected $signature = 'breaks:repair-negative {--apply : Actually write the fixes (without this it is a dry run)}';

    protected $description = 'Fix breaks saved with a wrong duration (negative, or ~24h) because break_end got the wrong calendar day';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        $fixable = [];
        $skipped = [];

        // The saved duration is wrong whenever it disagrees with the real
        // length, which is the gap between the two clock times wrapping past
        // midnight (see Breaks::effective_duration). That covers both the
        // negative values and the "23h 19m" ones for a 41-minute break.
        Breaks::whereNotNull('break_end')->orderBy('id')->chunkById(500, function ($rows) use (&$fixable, &$skipped) {
            foreach ($rows as $break) {
                $real = $break->effective_duration;

                if (abs((int) $break->duration - $real) <= 2) {
                    continue; // already correct
                }

                if ($break->is_countable) {
                    $fixable[] = [$break, Carbon::parse($break->break_start)->addSeconds($real), $real];
                } else {
                    $skipped[] = $break;
                }
            }
        });

        if (!$fixable && !$skipped) {
            $this->info('Every break already has a correct duration. Nothing to do.');

            return self::SUCCESS;
        }

        $this->table(
            ['id', 'user', 'break_start', 'old break_end', 'old duration', 'new break_end', 'new duration'],
            collect($fixable)->map(fn ($f) => [
                $f[0]->id, $f[0]->user_id, $f[0]->break_start, $f[0]->break_end, $f[0]->duration,
                $f[1]->format('Y-m-d H:i:s'), $f[2] . 's (' . round($f[2] / 60, 1) . ' min)',
            ])->all()
        );

        foreach ($skipped as $break) {
            $this->warn("Skipped #{$break->id}: length works out to " . round($break->effective_duration / 3600, 1) . ' h, not a believable break. Check it by hand.');
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
