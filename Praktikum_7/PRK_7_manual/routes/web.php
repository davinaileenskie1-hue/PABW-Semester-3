<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KontakController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('kontaks.index'); // Halaman awal langsung ke daftar kontak
});

// Halaman register dan login hanya untuk pengguna yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Halaman yang hanya dapat diakses setelah login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // CRUD Kontak dengan upload foto
    Route::resource('kontaks', KontakController::class);
});