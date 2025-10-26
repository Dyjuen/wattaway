<?php

namespace App\Events;

use App\Models\Device;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceOffline
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Device $device;

    /**
     * Create a new event instance.
     */
    public function __construct(Device $device)
    {
        $this->device = $device;
    }
}
