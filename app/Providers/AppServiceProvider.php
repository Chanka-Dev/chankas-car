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
        // Configurar paginador para usar Bootstrap 4
        \Illuminate\Pagination\Paginator::useBootstrapFour();
        
        // Forzar HTTPS cuando se accede por dominio o en producción
        if (
            config('app.env') === 'production' || 
            request()->server('HTTPS') === 'on' ||
            request()->server('HTTP_X_FORWARDED_PROTO') === 'https' ||
            str_contains(request()->getHost(), 'chankascar.com')
        ) {
            \URL::forceScheme('https');
        }
        
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
