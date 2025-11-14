<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only log API requests to avoid flooding the log with web requests
        if ($request->is('api/*')) {
            $log = [
                'URI' => $request->getUri(),
                'METHOD' => $request->getMethod(),
                'IP' => $request->ip(),
                'HEADERS' => $request->headers->all(),
                'BODY' => $request->all(),
            ];

            Log::info('Incoming API Request:', $log);
        }

        return $next($request);
    }
}
