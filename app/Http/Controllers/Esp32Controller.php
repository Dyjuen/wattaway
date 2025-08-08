<?php

// app/Http/Controllers/Esp32Controller.php

namespace App\Http\Controllers;

use App\Models\Esp32Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class Esp32Controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Add CORS headers for all responses
        $this->middleware(function ($request, $next) {
            $response = $next($request);
            
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin');
            
            return $response;
        });
        
        // Handle preflight OPTIONS request
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return Response::make('OK', 200, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin',
            ]);
        }
    }
    /**
     * Handle POST request from ESP32
     * Example payload from ESP32:
     * {
     *   "sensor": "gps",
     *   "time": 1351824120,
     *   "data": [48.756080, 2.302038]
     * }
     */
    /**
     * Handle POST request from ESP32
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
            
            // Store the raw message in the database
            $message = Esp32Message::createIncoming(
                json_encode($data),
                [
                    'endpoint' => '/api/http-post',
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                ]
            );
            
            // Handle the specific format from ESP32
            $state = $data['state'] ?? [];
            $reported = $state['reported'] ?? [];
            $location = $reported['location'] ?? [];
            
            // Extract location data
            $latitude = $location['latitude'] ?? null;
            $longitude = $location['longitude'] ?? null;
            
            // Here you can process/store the location data as needed
            
            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Location data received',
                'data' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'received_at' => now()->toDateTimeString()
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
     * Example payload:
     * {
     *   "state": {
     *     "reported": {
     *       "location": {
     *         "latitude": 22.54,
     *         "longitude": 88.72
     *       }
     *     },
     *     "desired": {}
     *   }
     * }
     */
    /**
     * Handle JSON data from ESP32
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleArduinoJson(Request $request)
    {
        try {
            $data = $request->json()->all();
            Log::info('Received JSON data from ESP32:', $data);
            
            // Store the raw message in the database
            $message = Esp32Message::createIncoming(
                json_encode($data),
                [
                    'endpoint' => '/api/arduino-json',
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                ]
            );
            
            // Extract location data if available
            $location = $data['state']['reported']['location'] ?? null;
            
            if ($location) {
                Log::info('Location data received:', [
                    'latitude' => $location['latitude'] ?? null,
                    'longitude' => $location['longitude'] ?? null,
                    'received_at' => now()->toDateTimeString()
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data received successfully',
                'timestamp' => now()->toDateTimeString()
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
