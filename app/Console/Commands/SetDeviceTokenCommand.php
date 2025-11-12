<?php

namespace App\Console\Commands;

use App\Models\Device;
use Illuminate\Console\Command;

class SetDeviceTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wattaway:set-device-token {device_id} {token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually set the API token for a specific device';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deviceId = $this->argument('device_id');
        $token = $this->argument('token');

        $device = Device::find($deviceId);

        if (! $device) {
            $this->error("Device with ID {$deviceId} not found.");

            return 1;
        }

        $device->api_token = $token;
        $device->save();

        $this->info("Successfully set the API token for device ID {$deviceId}.");

        return 0;
    }
}
