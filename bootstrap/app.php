<?php

use App\Http\AppMiddleware;
use App\Http\Middleware\PermissionMiddelware;
use App\Http\Middleware\RestrictIpAccess;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// RestrictIpAccess

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // ->withMiddleware(new AppMiddleware)
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->append(PermissionMiddelware::class);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('attendance:close-shift')
            ->dailyAt('04:15')
            ->timezone('Asia/Karachi');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
