<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\DeviceReading;
use App\Models\ChannelReading;
use Carbon\Carbon;

class HundredWhSeeder extends Seeder
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

        $this->command->info("Seeding 100Wh of device readings for device: {$device->name}");

        $endTime = Carbon::now();
        $startTime = $endTime->copy()->subHour();
        $intervalSeconds = 30;

        $totalReadings = $startTime->diffInSeconds($endTime) / $intervalSeconds;
        if ($totalReadings > 0) {
            $this->command->getOutput()->progressStart($totalReadings);
        }

        $voltage = 53.5;
        $totalPower = 100.0; // Constant power
        $powerDistribution = [1 => 0.5, 2 => 0.3, 3 => 0.2];

        for ($time = $startTime; $time->lessThanOrEqualTo($endTime); $time->addSeconds($intervalSeconds)) {
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
                    'relay_state' => 'on',
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
            }
            if ($totalReadings > 0) {
                $this->command->getOutput()->progressAdvance();
            }
        }

        if ($totalReadings > 0) {
            $this->command->getOutput()->progressFinish();
        }
        $this->command->info('100Wh device reading seeding completed.');
    }
}
