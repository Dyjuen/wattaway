<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

Route::middleware(['web', 'auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/messages', [AdminController::class, 'messages'])->name('messages.index');
    Route::get('/messages/{message}', [AdminController::class, 'showMessage'])->name('messages.show');
    Route::get('/devices', [AdminController::class, 'devices'])->name('devices');

    Route::resource('firmware', \App\Http\Controllers\Admin\FirmwareController::class);
    Route::get('firmware/{firmware}/download', [\App\Http\Controllers\Admin\FirmwareController::class, 'download'])
        ->name('firmware.download');

    Route::resource('provisioning-tokens', \App\Http\Controllers\Admin\ProvisioningTokenController::class)
        ->only(['index', 'show']);
    Route::post('provisioning-tokens/{provisioning_token}/revoke', [\App\Http\Controllers\Admin\ProvisioningTokenController::class, 'revoke'])
        ->name('provisioning-tokens.revoke');
    Route::get('provisioning-tokens/{provisioning_token}/qr', [\App\Http\Controllers\Admin\ProvisioningTokenController::class, 'generateQr'])
        ->name('provisioning-tokens.qr');
    Route::post('provisioning-tokens/export', [\App\Http\Controllers\Admin\ProvisioningTokenController::class, 'export'])
        ->name('provisioning-tokens.export');

    Route::resource('devices', \App\Http\Controllers\Admin\DeviceController::class)->only(['create', 'store', 'show']);
});
