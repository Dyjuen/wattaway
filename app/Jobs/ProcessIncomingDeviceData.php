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

    public function handle(): void
    {
        $device = Device::find($this->deviceId);

        if (!$device) {
            Log::warning("Device not found in ProcessIncomingDeviceData job: {$this->deviceId}");
            return;
        }

        $validator = Validator::make($this->data, [
            'voltage' => 'required|numeric',
            'current' => 'required|numeric',
            'power' => 'required|numeric',
            'energy' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            Log::warning("Invalid data in ProcessIncomingDeviceData job for device: {$this->deviceId}", ['errors' => $validator->errors()]);
            return;
        }

        $validatedData = $validator->validated();

        DB::transaction(function () use ($device, $validatedData) {
            Esp32MessageLog::create([
                'device_id' => $device->id,
                'content' => json_encode($validatedData),
                'direction' => 'incoming',
                'metadata' => [
                    'source' => 'mqtt',
                ],
                'ip_address' => null, // Not available from MQTT
                'endpoint' => 'mqtt',
                'payload' => json_encode($validatedData)
            ]);

            $device->update([
                'status' => 'online',
                'last_seen_at' => now(),
            ]);
        });
    }

    public function failed(Throwable $exception): void
    {
        Log::error("Job to process incoming device data failed for device: {$this->deviceId}", ['exception' => $exception]);
    }
}
