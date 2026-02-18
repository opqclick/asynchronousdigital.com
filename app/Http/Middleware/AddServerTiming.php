<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddServerTiming
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        $durationMs = (microtime(true) - $start) * 1000;

        $response->headers->set('Server-Timing', 'app;dur=' . number_format($durationMs, 2, '.', ''));

        return $response;
    }
}
