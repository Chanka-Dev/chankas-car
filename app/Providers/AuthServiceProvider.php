<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate para verificar si el usuario es admin
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        // Gate para verificar si el usuario es cajero o admin
        Gate::define('cajero', function ($user) {
            return $user->hasRole('admin', 'cajero');
        });

        // Gate para verificar si el usuario es técnico, cajero o admin
        Gate::define('tecnico', function ($user) {
            return $user->hasRole('admin', 'cajero', 'tecnico');
        });
    }
}
