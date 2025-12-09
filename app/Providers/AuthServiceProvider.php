<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate; // WAJIB: Tambahkan ini
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User; // WAJIB: Asumsi model User ada

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // ==========================================================
        // TAMBAHKAN DEFINISI GATE UNTUK ADMIN-ACCESS DI SINI
        // ==========================================================
        
        Gate::define('admin-access', function (User $user) {
            // Asumsi kolom role di tabel users memiliki nilai 'admin'
            return $user->role === 'admin';
        });
        
        // ==========================================================
    }
}