<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Account;
use App\Models\Device;
use App\Models\DeviceProvisioningToken;
use App\Models\Esp32MessageLog;

class WattawaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the wattaway account (or get existing one)
        $account = Account::firstOrCreate(
            ['email' => 'wattaway@gmail.com'],
            [
                'username' => 'wattaway',
                'password' => Hash::make('wattaway123'),
            ]
        );

        // Create 3 devices for the account
        $devices = [
            [
                'name' => 'Living Room Smart Socket',
                'description' => 'Main living room smart socket for lamp control',
                'status' => 'online',
            ],
            [
                'name' => 'Bedroom Device',
                'description' => 'Bedroom smart socket with timer functionality',
                'status' => 'offline',
            ],
            [
                'name' => 'Kitchen Appliance Controller',
                'description' => 'Kitchen smart socket for appliance management',
                'status' => 'online',
            ],
        ];

        foreach ($devices as $deviceData) {
            $device = Device::factory()->create([
                'account_id' => $account->id,
                'name' => $deviceData['name'],
                'description' => $deviceData['description'],
                'status' => $deviceData['status'],
                'last_seen_at' => now(),
            ]);

            $token = DeviceProvisioningToken::generate($device->serial_number, $device->hardware_id);
            $device->update(['provisioning_token_id' => $token->id]);

            $this->createDummyLogs($device);
        }
    }

    private function createDummyLogs(Device $device): void
    {
        $logTypes = [
            [
                'direction' => 'incoming',
                'content' => 'Device status: online, voltage: 220V, current: 0.5A',
                'endpoint' => '/api/device/status',
                'payload' => '{"status":"online","voltage":220,"current":0.5}',
            ],
            [
                'direction' => 'outgoing',
                'content' => 'Turn on device command sent',
                'endpoint' => '/api/device/control',
                'payload' => '{"command":"turn_on","device_id":' . $device->id . '}',
            ],
            [
                'direction' => 'incoming',
                'content' => 'Timer set: 2 hours, auto-off enabled',
                'endpoint' => '/api/device/timer',
                'payload' => '{"timer_duration":7200,"auto_off":true}',
            ],
            [
                'direction' => 'incoming',
                'content' => 'Energy usage: 150Wh consumed today',
                'endpoint' => '/api/device/energy',
                'payload' => '{"energy_consumed":150,"unit":"Wh"}',
            ],
        ];

        foreach ($logTypes as $logData) {
            Esp32MessageLog::create([
                'device_id' => $device->id,
                'content' => $logData['content'],
                'direction' => $logData['direction'],
                'metadata' => [
                    'device_name' => $device->name,
                    'timestamp' => now()->toISOString(),
                ],
                'ip_address' => '192.168.1.' . rand(100, 255),
                'endpoint' => $logData['endpoint'],
                'payload' => $logData['payload'],
            ]);
        }
    }
}
