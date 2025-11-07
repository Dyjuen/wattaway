<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Esp32MessageLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeviceDataService
{
    /**
     * Process and store incoming data from the MQTT pipeline.
     *
     * @param int $deviceId
     * @param array $data
     * @return void
     */
    public function logMqttMessage(int $deviceId, array $data): void
    {
        $device = Device::find($deviceId);

        if (!$device) {
            Log::warning("Device not found during MQTT data processing: {$deviceId}");
            return;
        }

        $validator = Validator::make($data, [
            'voltage' => 'required|numeric',
            'current' => 'required|numeric',
            'power' => 'required|numeric',
            'energy' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            Log::warning("Invalid MQTT data for device: {$deviceId}", ['errors' => $validator->errors()]);
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
                'voltage' => $validatedData['voltage'],
                'current' => $validatedData['current'],
                'power' => $validatedData['power'],
                'energy' => $validatedData['energy'],
            ]);

            $device->update([
                'status' => 'online',
                'last_seen_at' => now(),
            ]);
        });

        // TODO: In the future, we can also trigger threshold alerts here.
        // $this->checkPowerThresholdAlert($device, $validatedData['power']);
    }

    /**
     * Process and store incoming data for a device.
     *
     * @param Device $device
     * @param array $data
     * @return void
     * @throws ValidationException
     */
    public function processIncomingData(Device $device, array $data): void
    {
        $validator = Validator::make($data, [
            'voltage' => 'required|numeric',
            'channels' => 'required|array',
            'channels.*.channel' => 'required|integer',
            'channels.*.current' => 'required|numeric',
            'channels.*.power' => 'required|numeric',
        ]);

        $validatedData = $validator->validate();

        // Calculate aggregate values
        $totalCurrent = 0;
        $totalPower = 0;
        foreach ($validatedData['channels'] as $channel) {
            $totalCurrent += $channel['current'];
            $totalPower += $channel['power'];
        }

        Esp32MessageLog::create([
            'device_id' => $device->id,
            'content' => json_encode($validatedData),
            'direction' => 'incoming',
            'payload' => json_encode($validatedData),
            'voltage' => $validatedData['voltage'],
            'current' => $totalCurrent,
            'power' => $totalPower,
            'energy' => 0, // Placeholder
        ]);

        // Update device state caches
        $device->update(['last_seen_at' => now()]);
        Cache::forget("device:{$device->id}:latest_data");
        Cache::forget("device:{$device->id}:stats:daily");

        $this->checkPowerThresholdAlert($device, $totalPower);
    }

    /**
     * Get the latest data log for a device.
     *
     * @param Device $device
     * @return Esp32MessageLog|null
     */
    public function getDeviceLatestData(Device $device): ?Esp32MessageLog
    {
        return Cache::remember(
            "device:{$device->id}:latest_data",
            30,
            fn() => $device->esp32MessageLogs()->latest()->first()
        );
    }

    /**
     * Get the data history for a device for a given number of hours.
     *
     * @param Device $device
     * @param int $hours
     * @return Collection
     */
    public function getDeviceDataHistory(Device $device, int $hours = 24): Collection
    {
        return Cache::remember(
            "device:{$device->id}:history:{$hours}h",
            600,
            fn() => $device->esp32MessageLogs()
                ->where('created_at', '>=', now()->subHours($hours))
                ->get()
        );
    }

    /**
     * Calculate the power consumption for a device between two dates.
     *
     * @param Device $device
     * @param Carbon $start
     * @param Carbon $end
     * @return float
     */
    public function calculatePowerConsumption(Device $device, Carbon $start, Carbon $end): float
    {
        $cacheKey = "device:{$device->id}:power:{$start->timestamp}-{$end->timestamp}";

        return Cache::remember($cacheKey, 600, function () use ($device, $start, $end) {
            $energy = $device->esp32MessageLogs()
                ->whereBetween('created_at', [$start, $end])
                ->sum('payload->energy');

            return (float) $energy;
        });
    }

    /**
     * Check if the current power usage exceeds the device's configured threshold.
     *
     * @param Device $device
     * @param float $currentPower
     * @return bool
     */
    public function checkPowerThresholdAlert(Device $device, float $currentPower): bool
    {
        $threshold = $device->getConfig('power_threshold');

        if ($threshold && $currentPower > $threshold) {
            // In a real application, you would dispatch a notification here.
            // For now, we just log it.
            Log::warning("Device {$device->id} has exceeded its power threshold.", [
                'current_power' => $currentPower,
                'threshold' => $threshold,
            ]);

            return true;
        }

        return false;
    }
}
