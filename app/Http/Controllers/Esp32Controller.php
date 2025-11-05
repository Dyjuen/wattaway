<?php

// app/Http/Controllers/Esp32Controller.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceDataRequest;
use App\Models\Esp32MessageLog;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\MqttMessageLog;

class Esp32Controller extends Controller
{

    /**
     * Handle GET request from ESP32
     * Returns data in the format expected by the ESP32
     */
    public function sendGet()
    {
        // Response format that matches what the ESP32 expects to parse
        return response()->json([
            'data' => [
                'status',
                'success',
                'timestamp' => now()->toDateTimeString()
            ]
        ]);
    }

    public function settings()
    {
        // Get all devices for the authenticated user
        $account = Auth::guard('account')->user();
        if (!$account) {
            return redirect()->route('login');
        }

        $devices = $account->devices->map(function ($device) {
            return (object) [
                'id' => $device->id,
                'name' => $device->name,
                'device_id' => $device->id,
                'type' => 'esp32',
                'status' => $device->status,
                'configurations' => $this->getDeviceConfigurations($device->id)
            ];
        });

        return view('settings', compact('devices'));
    }

    public function updateConfiguration(Request $request, $deviceId)
    {
        $validator = Validator::make($request->all(), [
            'timer' => 'nullable|array',
            'timer.duration' => 'nullable|integer|min:1|max:1440',
            'timer.is_active' => 'nullable|boolean',
            'scheduler' => 'nullable|array',
            'scheduler.start_time' => 'nullable|date_format:H:i',
            'scheduler.end_time' => 'nullable|date_format:H:i|after:scheduler.start_time',
            'scheduler.is_active' => 'nullable|boolean',
            'watt_limit' => 'nullable|array',
            'watt_limit.limit' => 'nullable|integer|min:1|max:10000',
            'watt_limit.is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Store configurations in a simple way for now
            // In a real implementation, you'd want a proper device_configurations table
            $configurations = [];

            if ($request->has('timer')) {
                $configurations['timer'] = [
                    'duration' => $request->timer['duration'] ?? null,
                    'is_active' => $request->timer['is_active'] ?? true,
                ];
            }

            if ($request->has('scheduler')) {
                $configurations['scheduler'] = [
                    'start_time' => $request->scheduler['start_time'] ?? null,
                    'end_time' => $request->scheduler['end_time'] ?? null,
                    'is_active' => $request->scheduler['is_active'] ?? true,
                ];
            }

            if ($request->has('watt_limit')) {
                $configurations['watt_limit'] = [
                    'limit' => $request->watt_limit['limit'] ?? null,
                    'is_active' => $request->watt_limit['is_active'] ?? true,
                ];
            }

            // Store configuration in database using Esp32MessageLog
            Esp32MessageLog::create([
                'device_id' => (int) $deviceId,
                'content' => 'Device configuration updated',
                'direction' => 'outgoing',
                'metadata' => [
                    'configurations' => $configurations,
                    'updated_by' => 'user'
                ],
                'ip_address' => $request->ip(),
                'endpoint' => '/esp32/configuration/' . $deviceId,
                'payload' => json_encode($configurations)
            ]);

            return response()->json(['message' => 'Device configuration updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update device configuration'], 500);
        }
    }

    public function getConfiguration($deviceId)
    {
        // Get the latest configuration for this device
        $latestConfig = Esp32MessageLog::where('device_id', $deviceId)
            ->where('endpoint', '/esp32/configuration/' . $deviceId)
            ->latest()
            ->first();

        $configurations = [];
        if ($latestConfig && $latestConfig->payload) {
            $configurations = json_decode($latestConfig->payload, true);
        }

        return response()->json([
            'timer' => $configurations['timer'] ?? null,
            'scheduler' => $configurations['scheduler'] ?? null,
            'watt_limit' => $configurations['watt_limit'] ?? null,
        ]);
    }

    private function getDeviceConfigurations($deviceId)
    {
        $latestConfig = Esp32MessageLog::where('device_id', $deviceId)
            ->where('endpoint', '/esp32/configuration/' . $deviceId)
            ->latest()
            ->first();

        if ($latestConfig && $latestConfig->payload) {
            return json_decode($latestConfig->payload, true);
        }

        return [];
    }
}
