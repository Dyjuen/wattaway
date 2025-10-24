<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Esp32Controller;
use App\Http\Controllers\Esp32MessageController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\OtaController;

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
    Route::middleware(['auth.device', 'throttle:120,1'])->group(function () {
        Route::post('/device/data', [Esp32Controller::class, 'handleDeviceData']);
    });

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('devices')->group(function () {
        Route::get('/', [DeviceController::class, 'index']);
        Route::get('/{device}', [DeviceController::class, 'show']);
        Route::post('/{device}/control', [DeviceController::class, 'control'])->middleware('throttle:30,1');
        Route::post('/{device}/schedule', [DeviceController::class, 'setSchedule']);
        Route::get('/{device}/data', [DeviceController::class, 'getData']);
        Route::get('/{device}/history', [DeviceController::class, 'getHistory']);
    });

    Route::middleware(['auth.device', 'throttle:10,1'])->prefix('ota')->group(function () {
        Route::get('/check', [OtaController::class, 'checkUpdate']);
        Route::get('/download/{firmware}', [OtaController::class, 'downloadFirmware'])->name('api.ota.download');
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
