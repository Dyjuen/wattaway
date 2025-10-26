<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Device;
use App\Models\DeviceSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeviceScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_device()
    {
        $device = Device::factory()->create();
        $schedule = DeviceSchedule::factory()->create(['device_id' => $device->id]);

        $this->assertInstanceOf(Device::class, $schedule->device);
        $this->assertEquals($device->id, $schedule->device->id);
    }

    public function test_is_enabled_scope()
    {
        DeviceSchedule::factory()->create(['is_enabled' => true]);
        DeviceSchedule::factory()->create(['is_enabled' => false]);

        $this->assertEquals(1, DeviceSchedule::enabled()->count());
    }
}