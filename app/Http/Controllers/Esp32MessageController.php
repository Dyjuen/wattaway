<?php

namespace App\Http\Controllers;

use App\Models\Esp32Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class Esp32MessageController extends Controller
{
    /**
     * Display a listing of messages with optional filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Esp32Message::query();
            
            // Filter by direction if provided
            if ($direction = $request->input('direction')) {
                $query->where('direction', $direction);
            }
            
            // Filter by date range if provided
            if ($startDate = $request->input('start_date')) {
                $query->where('created_at', '>=', $startDate);
            }
            
            if ($endDate = $request->input('end_date')) {
                $query->where('created_at', '<=', $endDate . ' 23:59:59');
            }
            
            // Get messages since a specific timestamp (for polling)
            if ($since = $request->input('since')) {
                $query->where('created_at', '>', date('Y-m-d H:i:s', $since));
            }
            
            // Order by creation date, newest first
            $messages = $query->orderBy('created_at', 'desc')
                             ->limit(100) // Limit to prevent excessive data transfer
                             ->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $messages,
                'last_update' => now()->timestamp,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error retrieving ESP32 messages: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve messages',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    
    /**
     * Get recent messages for the control panel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages(Request $request): JsonResponse
    {
        try {
            $since = $request->query('since', 0);
            
            $query = Esp32Message::query()
                ->since($since)
                ->orderBy('created_at', 'desc')
                ->limit(50);
            
            $messages = $query->get();
            
            return response()->json([
                'status' => 'success',
                'messages' => $messages,
                'lastUpdate' => now()->timestamp,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getMessages: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve messages',
            ], 500);
        }
    }

    /**
     * Store a new message from ESP32.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->json()->all();
            
            // Log the raw request data for debugging
            Log::info('Received ESP32 message:', $data);
            
            // Create the message
            $message = Esp32Message::createIncoming(
                is_array($data) ? json_encode($data) : $data,
                [
                    'user_agent' => $request->userAgent(),
                    'headers' => $request->headers->all(),
                ]
            );
            
            // Broadcast the message to the control panel via WebSocket if needed
            // This would be implemented with Laravel Broadcasting
            
            return response()->json([
                'status' => 'success',
                'message' => 'Message received and stored',
                'id' => $message->id,
                'timestamp' => $message->created_at->toDateTimeString(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error storing ESP32 message: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to store message',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    
    /**
     * Clear all messages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear(): JsonResponse
    {
        try {
            $count = Esp32Message::query()->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => "Successfully deleted {$count} messages",
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing messages: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear messages',
            ], 500);
        }
    }
}
