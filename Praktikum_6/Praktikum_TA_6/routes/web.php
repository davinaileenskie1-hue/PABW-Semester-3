<?php

use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Models\Laporan;
use Illuminate\Support\Facades\Route;

// Halaman awal diarahkan ke form pelaporan yang dapat diakses tanpa login.
Route::get('/', function () {
    return redirect()->route('laporan.create');
});

// ==================== Route publik (warga) ====================
Route::get('/laporan/create', [LaporanController::class, 'create'])->name('laporan.create');
Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');
Route::get('/laporan/{laporan}/konfirmasi', [LaporanController::class, 'konfirmasi'])->name('laporan.konfirmasi');

// ==================== Route yang dilindungi middleware auth ====================
Route::get('/dashboard', function () {
    return view('dashboard', [
        'totalLaporan' => Laporan::count(),
        'laporanTerbaru' => Laporan::latest()->take(5)->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Halaman daftar laporan hanya boleh dibuka oleh petugas yang sudah login.
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
