<?php

// app/Http/Controllers/Esp32Controller.php

namespace App\Http\Controllers;

use App\Models\Esp32Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

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
}
