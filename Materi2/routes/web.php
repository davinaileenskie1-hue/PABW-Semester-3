<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/profile', function () {
    return view('profile');
})->name('halaman.profile');

Route::get('/about', function () {
    return view('about');
})->name('halaman.about');

Route::get('/formulir', [DataController::class, 'formulir'])->name('masuk.page');
Route::post('/tampilkan', [DataController::class, 'inputData'])->name('post.page');