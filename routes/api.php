<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Esp32Controller;
use App\Http\Controllers\Esp32MessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::middleware(['auth.device', 'throttle:60,1'])->group(function () {
        Route::post('/device/data', [Esp32Controller::class, 'handleDeviceData']);
    });
});

// API endpoints for ESP32 messages
Route::prefix('esp32')->group(function () {
    // Store a new message from ESP32
    Route::post('/messages', [Esp32MessageController::class, 'store']);
    
    // Get recent messages
    Route::get('/messages', [Esp32MessageController::class, 'index']);
    
    // Get recent messages (for control panel)
    Route::get('/messages/recent', [Esp32MessageController::class, 'getMessages']);
    
    // Get system status
    Route::get('/status', [Esp32MessageController::class, 'getStatus']);
});
