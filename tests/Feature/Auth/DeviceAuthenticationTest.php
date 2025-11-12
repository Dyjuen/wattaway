<?php

namespace Tests\Feature\Auth;

use App\Models\Device;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_device_can_authenticate_with_valid_token()
    {
        $device = Device::factory()->create();

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

        $response->assertStatus(200);
    }

    public function test_device_cannot_authenticate_with_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token_here',
        ])->postJson('/api/v1/device/data', []);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid Token']);
    }

    public function test_device_cannot_access_without_token()
    {
        $response = $this->postJson('/api/v1/device/data', []);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized']);
    }
}
