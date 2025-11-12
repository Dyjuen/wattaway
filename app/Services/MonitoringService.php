<?php

namespace App\Services;

use App\Events\DeviceOffline;
use App\Models\Device;
use Illuminate\Support\Facades\Log;

class MonitoringService
{
    public function checkDeviceStatus()
    {
        Device::where('status', 'online')
            ->where('last_seen_at', '<', now()->subMinutes(5))
            ->each(function ($device) {
                $device->update(['status' => 'offline']);
                DeviceOffline::dispatch($device);
            });
    }

    /**
     * Updates a device's status based on a message from MQTT.
     */
    public function updateStatusFromMqtt(int $deviceId, string $newStatus): void
    {
        $device = Device::find($deviceId);

        if (! $device) {
            Log::warning("Device not found for MQTT status update: {$deviceId}");

            return;
        }

        // Validate the status to prevent writing arbitrary values
        if ($newStatus !== 'online' && $newStatus !== 'offline') {
            Log::warning("Invalid status '{$newStatus}' received for device {$deviceId}");

            return;
        }

        $updatePayload = ['status' => $newStatus];

        // Also update last_seen_at when the device comes online via MQTT status.
        if ($newStatus === 'online') {
            $updatePayload['last_seen_at'] = now();
        }

        $device->update($updatePayload);
    }

    public function logDeviceConnection(Device $device): void
    {
        Log::channel('device')->info('Device connected', [
            'device_id' => $device->id,
            'device_name' => $device->name,
            'ip_address' => request()->ip(),
            'firmware_version' => $device->firmware_version,
        ]);

        $device->update([
            'status' => 'online',
            'last_seen_at' => now(),
        ]);
    }

    public function logDeviceDisconnection(Device $device): void
    {
        Log::channel('device')->info('Device disconnected', [
            'device_id' => $device->id,
            'device_name' => $device->name,
        ]);

        $device->update(['status' => 'offline']);
    }

    public function logCommandSent(Device $device, string $command): void
    {
        Log::channel('device')->info("Command sent to device {$device->id}", ['command' => $command]);
    }

    public function logCommandFailed(Device $device, string $command, string $reason): void
    {
        Log::channel('device')->error("Failed to send command to device {$device->id}", [
            'command' => $command,
            'reason' => $reason,
        ]);
    }

    public function logAnomalousReading(Device $device, array $data): void
    {
        if ($data['voltage'] > 250 || $data['voltage'] < 200) {
            Log::channel('device')->warning('Abnormal voltage detected', [
                'device_id' => $device->id,
                'voltage' => $data['voltage'],
                'threshold' => '200-250V',
            ]);

            // In a real application, you would dispatch a notification here.
        }
    }

    public function trackApiResponseTime(string $endpoint, float $duration): void
    {
        Log::channel('performance')->info("API response time for {$endpoint}: {$duration}ms");
    }
}
