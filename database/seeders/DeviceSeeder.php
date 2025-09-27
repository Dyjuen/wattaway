<?php

namespace Database\Seeders;

use App\Models\Esp32Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample ESP32 devices (IP addresses) for testing
        $esp32Devices = [
            [
                'ip_address' => '192.168.1.100',
                'device_name' => 'Living Room Socket',
                'location' => 'Living Room',
            ],
            [
                'ip_address' => '192.168.1.101',
                'device_name' => 'Kitchen Socket',
                'location' => 'Kitchen',
            ],
            [
                'ip_address' => '192.168.1.102',
                'device_name' => 'Bedroom Socket',
                'location' => 'Bedroom',
            ],
        ];

        foreach ($esp32Devices as $deviceData) {
            // Create sample ESP32 message records to simulate device communication
            Esp32Message::create([
                'endpoint' => '/esp32/configuration/' . $deviceData['ip_address'],
                'user_agent' => 'DeviceSeeder',
                'ip_address' => $deviceData['ip_address'],
                'payload' => json_encode([
                    'device_name' => $deviceData['device_name'],
                    'location' => $deviceData['location'],
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
                'arduino_time' => now()->toIso8601String(),
                'led_state' => 'online'
            ]);
        }

        $this->command->info('Created ' . count($esp32Devices) . ' sample ESP32 devices');
    }
}
