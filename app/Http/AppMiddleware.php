<?php

namespace App\Http;

use App\Http\Middleware\RestrictIpAccess;
use Illuminate\Foundation\Configuration\Middleware;


class AppMiddleware
{
    public function __invoke(Middleware $middleware)
    {
        $middleware->appendToGroup('web', RestrictIpAccess::class);
    }
}
