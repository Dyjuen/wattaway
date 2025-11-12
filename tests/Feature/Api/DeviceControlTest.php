<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\Device;
use App\Services\MqttPublishService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Mockery\MockInterface;
use Tests\TestCase;

class DeviceControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mock(MqttPublishService::class, function (MockInterface $mock) {
            $mock->shouldReceive('setRelayState')->andReturn(true);
        });
    }

    public function test_account_can_control_their_device()
    {
        $account = Account::factory()->create();
        $device = Device::factory()->create(['account_id' => $account->id]);

        Sanctum::actingAs($account);

        $response = $this->postJson("/api/v1/devices/{$device->id}/control", [
            'action' => 'on',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Command sent to device.']);
    }

    public function test_account_cannot_control_others_device()
    {
        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();
        $device = Device::factory()->create(['account_id' => $account2->id]);

        Sanctum::actingAs($account1);

        $response = $this->postJson("/api/v1/devices/{$device->id}/control", [
            'action' => 'on',
        ]);

        $response->assertStatus(403); // Forbidden
    }

    public function test_validates_control_action()
    {
        $account = Account::factory()->create();
        $device = Device::factory()->create(['account_id' => $account->id]);

        Sanctum::actingAs($account);

        $response = $this->postJson("/api/v1/devices/{$device->id}/control", [
            'action' => 'invalid_action',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['action']);
    }
}
