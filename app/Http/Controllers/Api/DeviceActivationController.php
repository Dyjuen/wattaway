<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceActivationController extends Controller
{
    public function __construct(
        private readonly AuditLog $auditLog,
    ) {}

    public function activate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'serial_number' => ['required', 'string'],
            'hardware_id' => ['required', 'string'],
            'firmware_version' => ['nullable', 'string'],
        ]);

        $device = Device::where('serial_number', $validated['serial_number'])
            ->where('status', 'pending_activation')
            ->first();

        if (!$device) {
            return response()->json(['status' => 'NOT_PAIRED'], 404);
        }

        if ($device->hardware_id !== $validated['hardware_id']) {
            $this->auditLog->create([
                'action' => 'device.activation_failed',
                'auditable_id' => $device->id,
                'auditable_type' => Device::class,
                'context' => [
                    'expected_hardware_id' => $device->hardware_id,
                    'received_hardware_id' => $validated['hardware_id'],
                    'ip_address' => $request->ip(),
                ]
            ]);

            return response()->json(['error' => 'Hardware verification failed'], 403);
        }

        $device->update([
            'status' => 'online',
            'last_seen_at' => now(),
            'firmware_version' => $validated['firmware_version'],
            'activated_at' => now(),
        ]);

        return response()->json([
            'device_id' => $device->id,
            'api_token' => $device->api_token,
            'mqtt_credentials' => [
                'host' => config('mqtt.host'),
                'port' => config('mqtt.port'),
                'username' => config('mqtt.username'),
                'password' => config('mqtt.password'),
                'topics' => [
                    'publish' => 'device/' . $device->id . '/data',
                    'subscribe' => 'device/' . $device->id . '/commands',
                ]
            ],
            'user' => [
                'id' => $device->account->id,
                'name' => $device->account->username,
                'email' => $device->account->email,
            ]
        ]);
    }
}
