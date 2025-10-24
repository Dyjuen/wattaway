<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Facades\Log;
use App\Models\Esp32MessageLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DeviceDataService
{
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
            'current' => 'required|numeric',
            'power' => 'required|numeric',
            'energy' => 'required|numeric',
        ]);

        $validatedData = $validator->validate();

        Esp32MessageLog::create([
            'device_id' => $device->id,
            'content' => json_encode($validatedData),
            'direction' => 'incoming',
            'payload' => json_encode($validatedData),
        ]);

        $device->update(['last_seen_at' => now()]);

        $this->checkPowerThresholdAlert($device, $validatedData['power']);
    }

    /**
     * Get the latest data log for a device.
     *
     * @param Device $device
     * @return Esp32MessageLog|null
     */
    public function getDeviceLatestData(Device $device): ?Esp32MessageLog
    {
        return $device->esp32MessageLogs()->latest()->first();
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
        return $device->esp32MessageLogs()
            ->where('created_at', '>=', now()->subHours($hours))
            ->get();
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
        $energy = $device->esp32MessageLogs()
            ->whereBetween('created_at', [$start, $end])
            ->sum('payload->energy');

        return (float) $energy;
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
