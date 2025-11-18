<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Esp32MessageLog;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devices = Device::all();

        if ($devices->isEmpty()) {
            $this->command->info('No devices found, skipping ESP32 message log seeding in DeviceSeeder.');
            return;
        }

        foreach ($devices as $device) {
            Esp32MessageLog::create([
                'device_id' => $device->id,
                'content' => 'Device configuration message',
                'direction' => 'incoming',
                'ip_address' => '192.168.1.' . rand(100, 200),
                'endpoint' => '/esp32/configuration/' . $device->id,
                'payload' => json_encode([
                    'device_name' => $device->name,
                    'location' => $device->description, // using description as location
                    'timer' => [
                        'duration' => 30,
                        'is_active' => true,
                    ],
                    'scheduler' => [
                        'start_time' => '08:00',
                        'end_time' => '22:00',
                        'is_active' => true,
                    ],
                    'watt_limit' => [
                        'limit' => 1000,
                        'is_active' => true,
                    ],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Created additional ESP32 message logs for existing devices.');
    }
}
