<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeviceController as AdminDeviceController;
use App\Http\Controllers\Admin\FirmwareController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ProvisioningTokenController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Device Management
    Route::get('/devices', [AdminDeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/create', [AdminDeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices', [AdminDeviceController::class, 'store'])->name('devices.store');
    Route::get('/devices/{device}', [AdminDeviceController::class, 'show'])->name('devices.show');

    // Message Logs
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');

    // Provisioning Tokens
    Route::get('/provisioning-tokens', [ProvisioningTokenController::class, 'index'])->name('provisioning-tokens.index');
    Route::get('/provisioning-tokens/{token}/qr', [ProvisioningTokenController::class, 'qrCode'])->name('provisioning-tokens.qr');
    Route::post('/provisioning-tokens/{token}/revoke', [ProvisioningTokenController::class, 'revoke'])->name('provisioning-tokens.revoke');

    // Firmware Management
    Route::get('/firmware', [FirmwareController::class, 'index'])->name('firmware.index');
    Route::post('/firmware', [FirmwareController::class, 'store'])->name('firmware.store');
    Route::delete('/firmware/{firmware}', [FirmwareController::class, 'destroy'])->name('firmware.destroy');
    Route::post('/firmware/trigger-ota', [FirmwareController::class, 'triggerOtaUpdate'])->name('firmware.trigger-ota');
});