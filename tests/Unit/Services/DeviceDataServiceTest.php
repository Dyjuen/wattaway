<?php

namespace Tests\Unit\Services;

use App\Models\Account;
use App\Models\Device;
use App\Services\DeviceDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceDataServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DeviceDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DeviceDataService::class);
    }

    public function test_processes_incoming_data_correctly()
    {
        $account = Account::factory()->create();
        $device = Device::factory()->create(['account_id' => $account->id]);

        $data = [
            'voltage' => 220.5,
            'current' => 5.2,
            'power' => 1146.6,
            'energy' => 25.5,
            'frequency' => 50.0,
            'power_factor' => 0.95,
        ];

        $this->service->processIncomingData($device, $data);

        $this->assertDatabaseHas('esp32messagelogs', [
            'device_id' => $device->id,
            'voltage' => 220.5,
            'current' => 5.2,
        ]);

        $device->refresh();
        $this->assertNotNull($device->last_seen_at);
    }

    public function test_detects_power_threshold_alert()
    {
        $device = Device::factory()->create();
        $device->setConfig('power_threshold', 1000);

        $result = $this->service->checkPowerThresholdAlert($device, 1500);

        $this->assertTrue($result);
    }

    public function test_logs_mqtt_message_correctly()
    {
        $device = Device::factory()->create();

        $data = [
            'voltage' => 230.1,
            'current' => 1.5,
            'power' => 345.15,
            'energy' => 10.2,
        ];

        $this->service->logMqttMessage($device->id, $data);

        $this->assertDatabaseHas('esp32messagelogs', [
            'device_id' => $device->id,
            'payload' => json_encode($data),
            'metadata->source' => 'mqtt',
        ]);

        $device->refresh();
        $this->assertNotNull($device->last_seen_at);
        $this->assertEquals('online', $device->status);
    }
}
