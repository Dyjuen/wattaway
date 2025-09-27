<?php

// app/Http/Controllers/Esp32Controller.php

namespace App\Http\Controllers;

use App\Models\Esp32Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class Esp32Controller extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use \Illuminate\Foundation\Validation\ValidatesRequests;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // No middleware needed here as CORS is handled by the CorsMiddleware
    }
    /**
     * Handle POST request from ESP32
     * Expected payload format:
     * {
     *   "time": "2025-08-08T10:30:00Z",  // ISO 8601 format
     *   "led_state": "on"                // or "off"
     * }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function receivePost(Request $request)
    {
        try {
            $data = $request->json()->all();
            
            // Log the received data
            Log::info('Received data from ESP32:', $data);
            
            // Validate the incoming data
            $validated = $request->validate([
                'time' => 'required|date',
                'led_state' => 'required|in:on,off',
            ]);
            
            // Store the message in the database
            $message = Esp32Message::createIncoming(
                json_encode($data),
                [
                    'endpoint' => '/api/http-post',
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                    'arduino_time' => $validated['time'],
                    'led_state' => $validated['led_state']
                ]
            );
            
            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Arduino data received',
                'data' => [
                    'arduino_time' => $validated['time'],
                    'led_state' => $validated['led_state'],
                    'server_time' => now()->toIso8601String()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing ESP32 data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error processing request',
                'error' => $e->getMessage()
            ], 400);
        }
    }

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

    /**
     * Handle JSON data from ESP32
     * Expected payload format:
     * {
     *   "time": "2025-08-08T10:30:00Z",  // ISO 8601 format
     *   "led_state": "on"                // or "off"
     * }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleArduinoJson(Request $request)
    {
        try {
            $data = $request->json()->all();
            Log::info('Received JSON data from ESP32:', $data);
            
            // Handle nested state.reported structure
            $state = $data['state'] ?? null;
            $reported = $state['reported'] ?? null;
            
            if (!$reported) {
                // If the data doesn't have the expected structure, try to process it directly
                $reported = $data;
                Log::warning('Using direct data structure instead of state.reported');
            }
            
            // Extract time and led_state from the reported data
            $arduinoTime = $reported['time'] ?? null;
            $ledState = $reported['led_state'] ?? 'off';
            
            // If time is not in the root, check in the reported object
            if (!$arduinoTime && isset($reported['state']['reported']['time'])) {
                $arduinoTime = $reported['state']['reported']['time'];
                $ledState = $reported['state']['reported']['led_state'] ?? $ledState;
            }
            
            // Validate the data
            if (!$arduinoTime || !strtotime($arduinoTime)) {
                // If time is not valid, use current time
                $arduinoTime = now()->toIso8601String();
                Log::warning('Using server time as Arduino time was not provided or invalid');
            }
            
            // Ensure led_state is valid
            $ledState = in_array(strtolower($ledState), ['on', 'off']) ? strtolower($ledState) : 'off';
            
            // Store the message in the database
            $message = Esp32Message::createIncoming(
                json_encode($data),
                [
                    'endpoint' => '/api/arduino-json',
                    'user_agent' => $request->userAgent() ?: 'ESP32HTTPClient',
                    'ip_address' => $request->ip() ?: '127.0.0.1',
                    'arduino_time' => $arduinoTime,
                    'led_state' => $ledState
                ]
            );
            
            // Log successful storage
            Log::info('Successfully stored ESP32 message', [
                'message_id' => $message->id,
                'arduino_time' => $arduinoTime,
                'led_state' => $ledState
            ]);
            
            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Arduino data received and processed',
                'data' => [
                    'arduino_time' => $arduinoTime,
                    'led_state' => $ledState,
                    'server_time' => now()->toIso8601String(),
                    'message_id' => $message->id
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing Arduino JSON: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error processing request',
                'error' => $e->getMessage()
            ], 400);
        }
    }
    public function settings()
    {
        // Get all ESP32 devices (messages) for the current user
        $devices = \App\Models\Esp32Message::where('ip_address', '!=', '127.0.0.1')
            ->distinct('ip_address')
            ->pluck('ip_address')
            ->map(function ($ip) {
                return (object) [
                    'id' => $ip,
                    'name' => 'Smart Socket',
                    'device_id' => $ip,
                    'type' => 'esp32',
                    'status' => 'online',
                    'configurations' => $this->getDeviceConfigurations($ip)
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

            // Store configuration in database (using Esp32Message table for now)
            \App\Models\Esp32Message::create([
                'endpoint' => '/esp32/configuration/' . $deviceId,
                'user_agent' => 'DeviceConfiguration',
                'ip_address' => $deviceId,
                'payload' => json_encode($configurations),
                'arduino_time' => now()->toIso8601String(),
                'led_state' => 'config'
            ]);

            return response()->json(['message' => 'Device configuration updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update device configuration'], 500);
        }
    }

    public function getConfiguration($deviceId)
    {
        // Get the latest configuration for this device
        $latestConfig = \App\Models\Esp32Message::where('ip_address', $deviceId)
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
        $latestConfig = \App\Models\Esp32Message::where('ip_address', $deviceId)
            ->where('endpoint', '/esp32/configuration/' . $deviceId)
            ->latest()
            ->first();

        if ($latestConfig && $latestConfig->payload) {
            return json_decode($latestConfig->payload, true);
        }

        return [];
    }
}
