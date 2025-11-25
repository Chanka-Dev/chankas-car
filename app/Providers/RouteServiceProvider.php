<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Models\Cargo;
use App\Models\Servicio;
use App\Models\Empleado;
use App\Models\Cliente;
use App\Models\Trabajo;
use App\Models\Proveedor;
use App\Models\GastoTaller;
use App\Models\PagoTecnico;
use App\Models\Inventario;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Rate Limiter
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Model Bindings
        Route::bind('cargo', function ($value) {
            return Cargo::where('id_cargo', $value)->firstOrFail();
        });

        Route::bind('servicio', function ($value) {
            return Servicio::where('id_servicio', $value)->firstOrFail();
        });

        Route::bind('empleado', function ($value) {
            return Empleado::where('id_empleado', $value)->firstOrFail();
        });

        Route::bind('cliente', function ($value) {
            return Cliente::where('id_cliente', $value)->firstOrFail();
        });

        Route::bind('trabajo', function ($value) {
            return Trabajo::where('id_trabajo', $value)->firstOrFail();
        });

        Route::bind('proveedor', function ($value) {
            return Proveedor::where('id_proveedor', $value)->firstOrFail();
        });

        Route::bind('gasto', function ($value) {
            return GastoTaller::where('id_gasto', $value)->firstOrFail();
        });

        Route::bind('inventario', function ($value) {
            return Inventario::where('id_inventario', $value)->firstOrFail();
        });

        Route::bind('pago', function ($value) {
            return PagoTecnico::where('id_pago', $value)->firstOrFail();
        });

        // Rutas
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}