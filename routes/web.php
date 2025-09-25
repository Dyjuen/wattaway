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

use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('home');
});
Route::get('/faq', function () {
    return view('faq');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard', function () {
    if (!auth()->guard('account')->check()) {
        return redirect()->route('login');
    }
    return view('dashboard');
})->name('dashboard');

Route::get('/settings', function () {
    if (!auth()->guard('account')->check()) {
        return redirect()->route('login');
    }
    return view('settings');
})->name('settings');

Route::get('/information', function () {
    if (!auth()->guard('account')->check()) {
        return redirect()->route('login');
    }
    return view('information');
})->name('information');


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
