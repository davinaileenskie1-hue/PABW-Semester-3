<?php

use Illuminate\Support\Facades\Route;
use Modules\Fasilitas\Http\Controllers\TitikSampahController;

/*
|--------------------------------------------------------------------------
| Route modul Fasilitas (modul utama)
|--------------------------------------------------------------------------
| Read  : semua user login
| CUD   : hanya petugas (middleware role)
*/

Route::middleware('auth')->group(function () {
    Route::get('TitikSampah', [TitikSampahController::class, 'index'])->name('TitikSampah.index');

    // Harus dideklarasikan sebelum fasilitas/{fasilitas} agar tidak dianggap parameter
    Route::get('TitikSampah/create', [TitikSampahController::class, 'create'])
        ->middleware('role:admin')
        ->name('TitikSampah.create');

    Route::post('TitikSampah', [TitikSampahController::class, 'store'])
        ->middleware('role:admin')
        ->name('TitikSampah.store');

    Route::get('TitikSampah/{TitikSampah}', [TitikSampahController::class, 'show'])->name('TitikSampah.show');

    Route::get('TitikSampah/{TitikSampah}/edit', [TitikSampahController::class, 'edit'])
        ->middleware('role:admin')
        ->name('TitikSampah.edit');

    Route::put('TitikSampah/{TitikSampah}', [TitikSampahController::class, 'update'])
        ->middleware('role:admin')
        ->name('TitikSampah.update');

    Route::delete('TitikSampah/{TitikSampah}', [TitikSampahController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('TitikSampah.destroy');
});
