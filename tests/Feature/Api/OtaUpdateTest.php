<?php

namespace Tests\Feature\Api;

use App\Models\Device;
use App\Models\FirmwareVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OtaUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_device_can_check_for_updates()
    {
        $firmware = FirmwareVersion::factory()->create([
            'version' => '1.1.0',
            'is_stable' => true,
        ]);
        $device = Device::factory()->create(['firmware_version_id' => $firmware->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$device->api_token,
            'X-Firmware-Version' => '1.0.0',
        ])->getJson('/api/v1/ota/check');

        $response->assertStatus(200);
        $response->assertJson([
            'update_available' => true,
            'version' => '1.1.0',
        ]);
    }

    public function test_device_with_latest_firmware_gets_no_update()
    {
        $firmware = FirmwareVersion::factory()->create([
            'version' => '1.1.0',
            'is_stable' => true,
        ]);
        $device = Device::factory()->create(['firmware_version_id' => $firmware->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$device->api_token,
            'X-Firmware-Version' => '1.1.0',
        ])->getJson('/api/v1/ota/check');

        $response->assertStatus(200);
        $response->assertJson(['update_available' => false]);
    }
}
