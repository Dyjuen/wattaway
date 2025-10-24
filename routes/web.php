<?php

use App\Http\Controllers\Esp32Controller;
use App\Http\Controllers\Esp32MessageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FirmwareController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('home');
});

// Health check endpoint for deployment monitoring
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toISOString()]);
});
Route::get('/faq', function () {
    return view('faq');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::middleware(['auth:account'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/settings', [Esp32Controller::class, 'settings'])->name('settings');

    Route::get('/information', function () {
        return view('information');
    })->name('information');

    // ESP32 Configuration Routes
    Route::prefix('esp32')->group(function () {
        Route::post('/{deviceId}/configuration', [Esp32Controller::class, 'updateConfiguration'])->name('esp32.configuration.update');
        Route::get('/{deviceId}/configuration', [Esp32Controller::class, 'getConfiguration'])->name('esp32.configuration.get');
    });

    // ESP32 Messages Management
    Route::prefix('esp32/messages')->group(function () {
        Route::get('/', [Esp32MessageController::class, 'index'])->name('esp32.messages.index');
        Route::get('/recent', [Esp32MessageController::class, 'getMessages'])->name('esp32.messages.recent');
        Route::delete('/clear', [Esp32MessageController::class, 'clear'])->name('esp32.messages.clear');
    });

    // ESP32 Control Panel (Public routes for ESP32 setup)
    Route::get('/control', function () {
        return view('esp32.control');
    })->name('esp32.control');

    Route::get('/wifisetup', function () {
        return view('esp32.wifisetup');
    })->name('esp32.wifisetup');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::resource('firmware', FirmwareController::class);
    Route::get('firmware/{firmware}/download', [FirmwareController::class, 'download'])
        ->name('firmware.download');
});
