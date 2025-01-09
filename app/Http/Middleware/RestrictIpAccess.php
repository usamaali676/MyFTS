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
    protected $allowedRanges = [
        '139.135.32.0/23',  // Range 1
        '182.188.0.0/16',
        '127.0.0.0/8',   // Range 2
    ];
    public function handle(Request $request, Closure $next): Response
    {
        $clientIp = $request->ip();
        // dd($clientIp);
        // Loop through each allowed IP range and check if the client's IP is within it
        foreach ($this->allowedRanges as $allowedRange) {
            // Get the network and netmask from the CIDR range
            list($network, $netmask) = explode('/', $allowedRange);

            // Convert the IP and network to long format
            $ipLong = ip2long($clientIp);
            // dd($ipLong);
            $networkLong = ip2long($network);
            $maskLong = ~((1 << (32 - $netmask)) - 1);

            // Check if the IP falls within the range
            if (($ipLong & $maskLong) === ($networkLong & $maskLong)) {
                return $next($request); // IP is allowed, continue the request
            }
        }

        // If no match is found, deny access
        abort(403, 'Access Denied');
    }
}
