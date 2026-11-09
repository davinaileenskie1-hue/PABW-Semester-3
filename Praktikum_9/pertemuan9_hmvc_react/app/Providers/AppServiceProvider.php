<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Login, register, dan verifikasi email ditangani oleh controller di
        // app/Http/Controllers/Auth, jadi rute bawaan Fortify tidak didaftarkan.
        Fortify::ignoreRoutes();
    }
}
