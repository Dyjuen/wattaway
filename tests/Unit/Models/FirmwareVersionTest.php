<?php

namespace Tests\Unit\Models;

use App\Models\Device;
use App\Models\FirmwareVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FirmwareVersionTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_many_devices()
    {
        $firmware = FirmwareVersion::factory()->create();
        Device::factory()->count(3)->create(['firmware_version_id' => $firmware->id]);

        $this->assertCount(3, $firmware->devices);
        $this->assertInstanceOf(Device::class, $firmware->devices->first());
    }

    public function test_stable_scope()
    {
        FirmwareVersion::factory()->create(['is_stable' => true]);
        FirmwareVersion::factory()->create(['is_stable' => false]);

        $this->assertEquals(1, FirmwareVersion::stable()->count());
    }
}
