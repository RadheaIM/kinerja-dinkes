<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; 
use Illuminate\Support\Facades\URL; // <-- WAJIB: Tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 1. Force Bootstrap Five untuk Pagination
        Paginator::useBootstrapFive(); 
        
        // 2. Fix masalah HTTPS Warning di Chrome/HP saat di Railway
        // Jika environment-nya adalah 'production' (seperti yang kita set di Variables Railway),
        // paksa Laravel untuk selalu menggunakan skema HTTPS.
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}