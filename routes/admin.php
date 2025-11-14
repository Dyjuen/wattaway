<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeviceController as AdminDeviceController;
use App\Http\Controllers\Admin\FirmwareController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Device Management
    Route::get('/devices', [AdminDeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/{device}', [AdminDeviceController::class, 'show'])->name('devices.show');

    // Firmware Management
    Route::get('/firmware', [FirmwareController::class, 'index'])->name('firmware.index');
    Route::post('/firmware', [FirmwareController::class, 'store'])->name('firmware.store');
    Route::delete('/firmware/{firmware}', [FirmwareController::class, 'destroy'])->name('firmware.destroy');
    Route::post('/firmware/trigger-ota', [FirmwareController::class, 'triggerOtaUpdate'])->name('firmware.trigger-ota');
});