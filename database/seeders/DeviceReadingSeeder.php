<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\DeviceReading;
use App\Models\ChannelReading;
use Carbon\Carbon;

class DeviceReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $device = Device::first();

        if (!$device) {
            $this->command->info('No device found. Skipping device reading seeding.');
            return;
        }

        $this->command->info("Seeding device readings for device: {$device->name}");

        $endTime = Carbon::now();
        $startTime = $endTime->copy()->subWeek();
        $intervalSeconds = 30;

        $totalReadings = $startTime->diffInSeconds($endTime) / $intervalSeconds;
        $this->command->getOutput()->progressStart($totalReadings);

        $voltage = 53.5;

        $powerDistribution = [1 => 0.5, 2 => 0.3, 3 => 0.2];

        for ($time = $startTime; $time->lessThanOrEqualTo($endTime); $time->addSeconds($intervalSeconds)) {
            $hour = $time->hour;

            // Simulate higher usage during the day (e.g., 8am to 8pm)
            if ($hour >= 8 && $hour < 20) {
                $totalPower = 10.0; // Higher power
            } else {
                $totalPower = 1.87; // Lower power
            }

            $deviceReading = DeviceReading::create([
                'device_id' => $device->id,
                'firmware_version' => '1.0.1',
                'timestamp' => $time,
                'voltage' => $voltage,
                'voltage_raw' => 456,
                'created_at' => $time,
                'updated_at' => $time,
            ]);

            for ($channel = 1; $channel <= 3; $channel++) {
                $power = $totalPower * $powerDistribution[$channel];
                $current = $power > 0 ? $power / $voltage : 0;
                ChannelReading::create([
                    'device_reading_id' => $deviceReading->id,
                    'channel' => $channel,
                    'current' => $current,
                    'current_raw' => round($current * 220), // Approximate raw value
                    'power' => $power,
                    'relay_state' => $power > 0 ? 'on' : 'off',
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
            }
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info('Device reading seeding completed.');
    }
}
