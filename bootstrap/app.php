<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/health',
        then: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.device' => \App\Http\Middleware\AuthenticateDevice::class,
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'throttle.device.commands' => \App\Http\Middleware\ThrottleDeviceCommands::class,
        ]);

        // Register CORS middleware
        $middleware->append(\App\Http\Middleware\CorsMiddleware::class);

        // Trust proxies for Coolify deployment
        $middleware->trustProxies(at: '*');

        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, \Illuminate\Http\Request $request) {
            return response()->json([
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $e->getHeaders()['Retry-After'] ?? 60,
            ], 429);
        });
    })
    ->withSchedule(function ($schedule) {
        $schedule->command('schedule:process')->everyMinute();
    })->create();
