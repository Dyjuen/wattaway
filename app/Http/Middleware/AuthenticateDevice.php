<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;

class AuthenticateDevice
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $device = Device::where('api_token', $token)->first();

        if (! $device) {
            return response()->json(['message' => 'Invalid Token'], 401);
        }

        $request->setUserResolver(function () use ($device) {
            return $device;
        });

        return $next($request);
    }
}
