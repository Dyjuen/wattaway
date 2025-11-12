<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Device;
use App\Policies\DevicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Device::class => DevicePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Auth::viaRequest('account-token', function (Request $request) {
            $token = $request->bearerToken();

            if ($token) {
                return Account::where('api_token', $token)->first();
            }

            return null;
        });
    }
}
