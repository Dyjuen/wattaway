<?php

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
use App\Http\Controllers\DevicePairingController;

Route::get('/', function () {
    return view('home');
})->name('home');

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
    Route::get('/dashboard', [\App\Http\Controllers\DeviceController::class, 'index'])->name('dashboard');
    Route::get('/devices/{device}', [\App\Http\Controllers\DeviceController::class, 'show'])->name('devices.show');

    Route::get('/style-guide', function () {
        return view('style-guide');
    })->name('style-guide');

    Route::get('/settings', function () {
        return view('settings', [
            'account' => auth()->user(),
            'devices' => auth()->user()->devices,
        ]);
    })->name('settings');

    Route::post('/settings/password', [AuthController::class, 'updatePassword'])->name('password.update');

    Route::get('/information', function () {
        return view('information');
    })->name('information');

    Route::get('/pair', function () {
        return view('pairing.scan');
    })->name('pairing.scan');

    Route::get('/devices', function () {
        $devices = auth()->user()->devices;
        return view('devices.index', compact('devices'));
    })->name('devices.index');
    Route::get('/pairing/confirm', [DevicePairingController::class, 'showConfirmPairing'])->name('pairing.confirm');
    Route::post('/pairing/confirm', [DevicePairingController::class, 'pairDevice'])->name('pairing.pair');
});

Route::get('/provision/{token}', [DevicePairingController::class, 'handlePublicScan'])->name('pairing.public-scan');


