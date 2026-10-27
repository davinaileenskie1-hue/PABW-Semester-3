<?php

use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Form pelaporan warga: dapat diakses tanpa login
Route::resource('laporan', LaporanController::class)->only(['create', 'store']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD Laporan oleh petugas: hanya dapat diakses setelah login
    Route::resource('laporan', LaporanController::class)->except(['create', 'store']);
});

require __DIR__.'/auth.php';
