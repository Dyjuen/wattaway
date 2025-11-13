<?php

return [
    'rate_per_kwh' => env('RATE_PER_KWH', 0.15),

    /*
    |--------------------------------------------------------------------------
    | Device Offline Threshold
    |--------------------------------------------------------------------------
    |
    | The number of minutes after which a device is considered offline if it
    | hasn't been seen. This is used in the Device model's status
    | accessor to dynamically determine the device's online status.
    |
    */
    'device_offline_threshold' => env('DEVICE_OFFLINE_THRESHOLD_MINUTES', 5),
];
