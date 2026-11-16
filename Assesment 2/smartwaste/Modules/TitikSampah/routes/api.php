<?php

use Illuminate\Support\Facades\Route;
use Modules\TitikSampah\Http\Controllers\TitikSampahController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('titiksampahs', TitikSampahController::class)->names('titiksampah');
});
