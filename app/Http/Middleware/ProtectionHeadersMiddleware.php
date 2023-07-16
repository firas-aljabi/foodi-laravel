<?php

namespace App\Http\Middleware;

use Closure;

class ProtectionHeadersMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('X-Frame-Options', 'SAMEORIGIN');
        $response->header('X-XSS-Protection', '1; mode=block');
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        

        return $response;
    }
}
