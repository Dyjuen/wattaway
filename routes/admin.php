<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

Route::middleware(['web', 'auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/messages', [AdminController::class, 'messages'])->name('messages.index');
    Route::get('/messages/{message}', [AdminController::class, 'showMessage'])->name('messages.show');
    Route::get('/devices', [AdminController::class, 'devices'])->name('devices');
});
