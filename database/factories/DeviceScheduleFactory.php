<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeviceSchedule>
 */
class DeviceScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'device_id' => Device::factory(),
            'name' => 'Test Schedule',
            'action' => 'on',
            'schedule_type' => 'daily',
            'scheduled_time' => '12:00:00',
            'is_enabled' => true,
        ];
    }
}
