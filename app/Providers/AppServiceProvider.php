<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        // Blade directive para verificar si puede editar
        Blade::if('canEdit', function () {
            return auth()->check() && auth()->user()->hasRole('admin', 'cajero', 'tecnico');
        });

        // Blade directive para verificar si es admin
        Blade::if('isAdmin', function () {
            return auth()->check() && auth()->user()->isAdmin();
        });

        // Blade directive para verificar si puede gestionar (admin o cajero)
        Blade::if('canManage', function () {
            return auth()->check() && auth()->user()->hasRole('admin', 'cajero');
        });
    }
}
