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

        $schedules = DeviceSchedule::enabled()->dueNow()->get();

        foreach ($schedules as $schedule) {
            if ($schedule->shouldExecuteNow()) {
                $this->info("Executing schedule: {$schedule->name}");
                Log::info("Executing schedule: {$schedule->name}");
                $schedule->execute();
            }
        }

        $this->info('Done processing device schedules.');
        Log::info('Done processing device schedules.');
    }
}
