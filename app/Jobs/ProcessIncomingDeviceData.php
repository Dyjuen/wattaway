<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Device;
use App\Models\Esp32MessageLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\DeviceDataService;
use Throwable;

class ProcessIncomingDeviceData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(
        public int $deviceId,
        public array $data
    ) {}

    public function handle(DeviceDataService $deviceDataService): void
    {
        $deviceDataService->logMqttMessage($this->deviceId, $this->data);
    }

    public function failed(Throwable $exception): void
    {
        Log::error("Job to process incoming device data failed for device: {$this->deviceId}", ['exception' => $exception]);
    }
}
