<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictIpAccess
{
    /**
     * Handle an incoming request.

     *
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $allowedIps = [
        '139.135.32.81',  // Replace with the allowed IP address
        '127.0.0.1',
        '182.188.109.240'
        // You can add more IPs here if needed
    ];
    public function handle(Request $request, Closure $next): Response
    {
        // dd($request->ip());
        if (!in_array($request->ip(), $this->allowedIps)) {
            // If the IP is not allowed, return a 403 Forbidden response
            abort(403, 'Access Denied');
        }
        return $next($request);
    }
}
