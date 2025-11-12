<?php

namespace App\Console\Commands;

use App\Models\DeviceSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessSchedulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all due device schedules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing device schedules...');
        Log::info('Processing device schedules...');

        // Eager load the device relationship
        $schedules = DeviceSchedule::with('device')->enabled()->dueNow()->get();

        foreach ($schedules as $schedule) {
            // Check if the device relationship is loaded and not null
            if ($schedule->device && $schedule->shouldExecuteNow()) {
                $this->info("Executing schedule: {$schedule->name} for device ID: {$schedule->device->id}");
                Log::info("Executing schedule: {$schedule->name} for device ID: {$schedule->device->id}");
                $schedule->execute();
            } elseif (! $schedule->device) {
                $this->warn("Skipping schedule ID: {$schedule->id} because its device is missing.");
                Log::warning("Skipping schedule ID: {$schedule->id} because its device is missing.");
            }
        }

        $this->info('Done processing device schedules.');
        Log::info('Done processing device schedules.');
    }
}
