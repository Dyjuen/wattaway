<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\Helpers\ScheduleHelper;
use App\Models\DeviceSchedule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_schedule_is_due()
    {
        $schedule = DeviceSchedule::factory()->create([
            'schedule_type' => 'daily',
            'scheduled_time' => '10:00',
            'is_enabled' => true,
        ]);

        $now = Carbon::create(2025, 10, 26, 10, 0, 0);

        $this->assertTrue(ScheduleHelper::isDue($schedule, $now));
    }

    public function test_weekly_schedule_is_due_on_correct_day()
    {
        $schedule = DeviceSchedule::factory()->create([
            'schedule_type' => 'weekly',
            'scheduled_time' => '12:00',
            'days_of_week' => [1, 3, 5], // Mon, Wed, Fri
            'is_enabled' => true,
        ]);

        $now = Carbon::create(2025, 10, 27, 12, 0, 0); // A Monday

        $this->assertTrue(ScheduleHelper::isDue($schedule, $now));
    }

    public function test_weekly_schedule_is_not_due_on_incorrect_day()
    {
        $schedule = DeviceSchedule::factory()->create([
            'schedule_type' => 'weekly',
            'scheduled_time' => '12:00',
            'days_of_week' => [1, 3, 5], // Mon, Wed, Fri
            'is_enabled' => true,
        ]);

        $now = Carbon::create(2025, 10, 26, 12, 0, 0); // A Sunday

        $this->assertFalse(ScheduleHelper::isDue($schedule, $now));
    }

    public function test_disabled_schedule_is_not_due()
    {
        $schedule = DeviceSchedule::factory()->create([
            'schedule_type' => 'daily',
            'scheduled_time' => '10:00',
            'is_enabled' => false,
        ]);

        $now = Carbon::create(2025, 10, 26, 10, 0, 0);

        $this->assertFalse(ScheduleHelper::isDue($schedule, $now));
    }
}
