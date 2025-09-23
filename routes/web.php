<?php

use App\Http\Controllers\Esp32MessageController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('home');
});

Route::get('/faq', function () {
    return view('faq');
});

// ESP32 Control Panel
Route::get('/control', function () {
    return view('esp32.control');
})->name('esp32.control');

Route::get('/wifisetup', function () {
    return view('esp32.wifisetup');
})->name('esp32.wifisetup');

// ESP32 Messages Management
Route::prefix('esp32/messages')->group(function () {
    Route::get('/', [Esp32MessageController::class, 'index'])->name('esp32.messages.index');
    Route::get('/recent', [Esp32MessageController::class, 'getMessages'])->name('esp32.messages.recent');
    Route::delete('/clear', [Esp32MessageController::class, 'clear'])->name('esp32.messages.clear');
});
