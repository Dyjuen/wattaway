<?php

use Illuminate\Support\Str;

return [
    'host' => env('MQTT_HOST', 'localhost'),
    'port' => env('MQTT_PORT', 1883),
    'username' => env('MQTT_USERNAME'),
    'password' => env('MQTT_PASSWORD'),
    'client_id' => env('MQTT_CLIENT_ID', 'laravel_'.Str::random(8)),
    'topics' => [
        'data' => 'devices/+/data',
        'commands' => 'devices/{device_id}/commands',
        'status' => 'devices/+/status',
    ],
];
