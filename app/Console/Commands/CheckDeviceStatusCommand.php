<?php

namespace App\Console\Commands;

use App\Services\MonitoringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckDeviceStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devices:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for devices that have gone offline';

    /**
     * Execute the console command.
     */
    public function handle(MonitoringService $monitoringService)
    {
        Log::info('Running scheduled check for offline devices...');

        $monitoringService->checkDeviceStatus();

        Log::info('Finished checking for offline devices.');

        $this->info('Offline device check complete.');
    }
}
