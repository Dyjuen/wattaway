<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Esp32MessageLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DeviceDataService
{
    /**
     * Process and store incoming data from the MQTT pipeline.
     */
    public function logMqttMessage(int $deviceId, array $data): void
    {
        $device = Device::find($deviceId);

        if (! $device) {
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
     * @throws ValidationException
     */
    public function processIncomingData(Device $device, array $data): void
    {
        $validator = Validator::make($data, [
            'firmware_version' => 'required|string|max:255',
            'timestamp' => 'required|date_format:Y-m-d\TH:i:s\Z',
            'voltage' => 'required|numeric',
            'voltage_raw' => 'required|integer',
            'channels' => 'required|array|min:1',
            'channels.*.channel' => 'required|integer',
            'channels.*.current' => 'required|numeric',
            'channels.*.current_raw' => 'required|integer',
            'channels.*.power' => 'required|numeric',
            'channels.*.relay_state' => 'required|in:on,off',
        ]);

        $validatedData = $validator->validate();

        DB::transaction(function () use ($device, $validatedData) {
            $deviceReading = $device->deviceReadings()->create([
                'firmware_version' => $validatedData['firmware_version'],
                'timestamp' => Carbon::parse($validatedData['timestamp']),
                'voltage' => $validatedData['voltage'],
                'voltage_raw' => $validatedData['voltage_raw'],
            ]);

            $channelReadings = [];
            $totalPower = 0;
            foreach ($validatedData['channels'] as $channelData) {
                $channelReadings[] = [
                    'channel' => $channelData['channel'],
                    'current' => $channelData['current'],
                    'current_raw' => $channelData['current_raw'],
                    'power' => $channelData['power'],
                    'relay_state' => $channelData['relay_state'],
                ];
                $totalPower += $channelData['power'];
            }

            $deviceReading->channelReadings()->createMany($channelReadings);

            // Update device state caches
            $device->update(['last_seen_at' => now()]);
            Cache::forget("device:{$device->id}:latest_data");
            Cache::forget("device:{$device->id}:stats:daily");

            $this->checkPowerThresholdAlert($device, $totalPower);
        });
    }

    /**
     * Get the latest data log for a device.
     */
    public function getDeviceLatestData(Device $device): ?Esp32MessageLog
    {
        return Cache::remember(
            "device:{$device->id}:latest_data",
            30,
            fn () => $device->esp32MessageLogs()->latest()->first()
        );
    }

    /**
     * Get the data history for a device for a given number of hours.
     */
    public function getDeviceDataHistory(Device $device, int $hours = 24): Collection
    {
        return Cache::remember(
            "device:{$device->id}:history:{$hours}h",
            600,
            fn () => $device->esp32MessageLogs()
                ->where('created_at', '>=', now()->subHours($hours))
                ->get()
        );
    }

    /**
     * Calculate the power consumption for a device between two dates.
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
