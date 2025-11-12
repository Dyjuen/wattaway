<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        if (app()->environment('production')) {
            // Enforce HTTPS
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

            // Content Security Policy
            $response->headers->set('Content-Security-Policy',
                "default-src 'self'; ".
                "script-src 'self'; ".
                "style-src 'self'; ".
                "font-src 'self'; ".
                "img-src 'self' data: https:; ".
                "connect-src 'self' wss: https:;"
            );
        } else {
            // Allow dev tools in development
            $response->headers->set('Content-Security-Policy',
                "default-src 'self' 'unsafe-inline' 'unsafe-eval'; ".
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com; ".
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; ".
                "font-src 'self' https://fonts.gstatic.com; ".
                "img-src 'self' data: https:; ".
                "connect-src 'self' wss: https:;"
            );
        }

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy (formerly Feature Policy)
        $response->headers->set('Permissions-Policy',
            'geolocation=(), microphone=(), camera=()'
        );

        return $response;
    }
}
