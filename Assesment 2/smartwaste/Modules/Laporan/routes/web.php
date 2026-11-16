<?php

use Illuminate\Support\Facades\Route;
use Modules\Laporan\Http\Controllers\LaporanController;
use Modules\Pengaduan\Http\Controllers\LaporanController as ControllersLaporanController;

/*
|--------------------------------------------------------------------------
| Route modul Pengaduan (modul kedua, berelasi dengan Fasilitas & User)
|--------------------------------------------------------------------------
| index  : semua user login (mahasiswa hanya melihat miliknya)
| store  : hanya mahasiswa (AJAX / JSON)
| update & destroy : hanya petugas
*/

Route::middleware('auth')->group(function () {
    Route::get('Laporan', [ControllersLaporanController::class, 'index'])->name('Laporan.index');
    Route::post('Laporan', [ControllersLaporanController::class, 'store'])
        ->middleware('role:warga')
        ->name('Laporan.store');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::put('Laporan/{Laporan}', [ControllersLaporanController::class, 'update'])->name('Laporan.update');
    Route::delete('Laporan/{Laporan}', [ControllersLaporanController::class, 'destroy'])->name('Laporan.destroy');
});
