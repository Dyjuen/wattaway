<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Device;
use App\Services\MqttPublishService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpMqtt\Client\MqttClient;
use Mockery;

class MqttPublishServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_mqtt_command()
    {
        $device = Device::factory()->create(['id' => 1]);

        $service = new MqttPublishService();

        $mqttClientMock = Mockery::spy(MqttClient::class);
        $reflection = new \ReflectionClass($service);
        $reflection_property = $reflection->getProperty('mqtt');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($service, $mqttClientMock);

        $result = $service->sendCommand($device, 'set_relay_state', ['state' => 'on']);

        $this->assertTrue($result);
        $mqttClientMock->shouldHaveReceived('publish')->once();
    }
}