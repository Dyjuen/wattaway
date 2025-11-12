<?php

namespace App\Jobs;

use App\Models\Device;
use App\Services\DeviceDataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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
        $device = Device::findOrFail($this->deviceId);
        $deviceDataService->processIncomingData($device, $this->data);
    }

    public function failed(Throwable $exception): void
    {
        Log::error("Job to process incoming device data failed for device: {$this->deviceId}", ['exception' => $exception]);
    }
}
