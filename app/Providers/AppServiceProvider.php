<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use PhpMqtt\Client\MqttClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MqttClient::class, function ($app) {
            $host = config('mqtt.host');
            $port = config('mqtt.port');
            $clientId = config('mqtt.client_id');
            return new MqttClient($host, $port, $clientId);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

