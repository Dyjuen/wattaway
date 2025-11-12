<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Esp32Controller;
use App\Http\Controllers\Esp32MessageController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\OtaController;
use App\Http\Controllers\Api\DevicePairingController;
use App\Http\Controllers\Api\DeviceActivationController;
use App\Http\Controllers\Api\DeviceControlController;

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

    });

    Route::middleware(['auth:sanctum'])->prefix('devices')->group(function () {
        Route::get('/', [DeviceController::class, 'index']);
        Route::get('/{device}', [DeviceController::class, 'show']);
        Route::post('/{device}/control', [DeviceController::class, 'control'])->middleware('throttle:30,1');
        Route::post('/{device}/relay', [DeviceControlController::class, 'updateRelayState']);
        Route::post('/{device}/schedule', [DeviceController::class, 'setSchedule']);
        Route::get('/{device}/data', [DeviceController::class, 'getData']);
        Route::get('/{device}/history', [DeviceController::class, 'getHistory']);
        Route::get('/{device}/readings', [DeviceController::class, 'readings']);
        Route::get('/{device}/configuration', [DeviceController::class, 'getConfiguration']);
        Route::post('/{device}/configuration/{type}', [DeviceController::class, 'updateConfiguration']);
    });

    Route::middleware(['auth.device', 'throttle:10,1'])->prefix('ota')->group(function () {
        Route::get('/check', [OtaController::class, 'checkUpdate']);
        Route::get('/download/{firmware}', [OtaController::class, 'downloadFirmware'])->name('api.ota.download');
    });
});

// Authenticated user routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Device pairing endpoints
    Route::post('/pairing/validate', [DevicePairingController::class, 'validateToken']);
    Route::post('/pairing/pair', [DevicePairingController::class, 'pairDevice']);
    Route::delete('/devices/{device}/unpair', [DevicePairingController::class, 'unpairDevice']);
});

// Device activation (no user auth, device self-identifies)
Route::post('/v1/device/activate', [DeviceActivationController::class, 'activate']);