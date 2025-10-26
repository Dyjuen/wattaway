<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Device;
use App\Services\MonitoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use App\Events\DeviceOffline;

class MonitoringServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_detects_offline_device()
    {
        Event::fake();

        $device = Device::factory()->create([
            'last_seen_at' => now()->subMinutes(10),
            'status' => 'online',
        ]);

        $service = new MonitoringService();
        $service->checkDeviceStatus();

        $device->refresh();
        $this->assertEquals('offline', $device->status);
        Event::assertDispatched(DeviceOffline::class, function ($event) use ($device) {
            return $event->device->id === $device->id;
        });
    }
}