<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/form', [App\Http\Controllers\MahasiswaController::class, 'form']);
Route::post('/simpan', [App\Http\Controllers\MahasiswaController::class, 'simpan']);
Route::get('/daftar-mahasiswa', [App\Http\Controllers\MahasiswaController::class, 'daftar']);
