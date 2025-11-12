<?php

namespace App\Helpers;

use App\Models\DeviceSchedule;
use Carbon\Carbon;

class ScheduleHelper
{
    /**
     * Check if a given schedule is due to run at a specific time.
     */
    public static function isDue(DeviceSchedule $schedule, Carbon $now): bool
    {
        if (! $schedule->is_enabled) {
            return false;
        }

        $scheduledTime = Carbon::parse($schedule->scheduled_time, $now->timezone);

        if ($scheduledTime->format('H:i') !== $now->format('H:i')) {
            return false;
        }

        switch ($schedule->schedule_type) {
            case 'once':
                return $scheduledTime->isSameDay($now);
            case 'daily':
                return true;
            case 'weekly':
                $days = $schedule->days_of_week ?? [];

                return in_array($now->dayOfWeekIso, $days);
        }

        return false;
    }
}
