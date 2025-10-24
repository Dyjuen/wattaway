<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Device;

class AuthenticateDevice
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
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $device = Device::where('api_token', $token)->first();

        if (!$device) {
            return response()->json(['message' => 'Invalid Token'], 401);
        }

        $request->setUserResolver(function () use ($device) {
            return $device;
        });

        return $next($request);
    }
}
