<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\Device;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeviceDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_rate_limiting_on_device_data_endpoint()
    {
        $device = Device::factory()->create();

        // Rate limit is 60/minute as per the user prompt, let's test that
        for ($i = 0; $i < 61; $i++) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer '.$device->api_token,
            ])->postJson('/api/v1/device/data', [
                'voltage' => 220,
                'current' => 5,
                'power' => 1100,
                'energy' => 10,
                'frequency' => 50,
                'power_factor' => 0.9,
            ]);

            if ($i < 60) {
                $response->assertStatus(200);
            } else {
                $response->assertStatus(429); // Too Many Requests
            }
        }
    }

    public function test_account_can_create_schedule()
    {
        $account = Account::factory()->create();
        $device = Device::factory()->create(['account_id' => $account->id]);

        Sanctum::actingAs($account);

        $response = $this->postJson("/api/v1/devices/{$device->id}/schedule", [
            'name' => 'Morning ON',
            'action' => 'on',
            'schedule_type' => 'daily',
            'scheduled_time' => '06:00',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('device_schedules', [
            'device_id' => $device->id,
            'name' => 'Morning ON',
            'scheduled_time' => '06:00:00',
        ]);
    }
}
