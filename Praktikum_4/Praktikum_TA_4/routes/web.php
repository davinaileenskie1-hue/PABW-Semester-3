<?php

use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('laporan.index');
});

// Halaman daftar laporan banjir.
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

// Halaman form pelaporan banjir.
Route::get('/laporan/create', [LaporanController::class, 'create'])->name('laporan.create');

// Memproses form pelaporan (method POST) dan menyimpan ke database.
Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');

// Halaman konfirmasi data laporan yang tersimpan.
Route::get('/laporan/{laporan}/konfirmasi', [LaporanController::class, 'konfirmasi'])->name('laporan.konfirmasi');
