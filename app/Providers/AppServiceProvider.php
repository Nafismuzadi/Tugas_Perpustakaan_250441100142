<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Peminjaman;

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
        // Paksa skema HTTPS di production agar aset CSS/JS tidak terblokir (Mixed Content)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Soal 3: Sentralisasi Otorisasi Gate untuk Peminjaman (Menambal IDOR)
        Gate::define('view-peminjaman', function (User $user, Peminjaman $peminjaman) {
            return $user->hasRole(['Admin', 'Petugas']) || $user->id === $peminjaman->user_id;
        });
    }
}