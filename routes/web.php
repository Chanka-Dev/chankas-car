<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TrabajoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\GastoTallerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PublicController;

// Ruta pública - Landing Page
Route::get('/', [PublicController::class, 'index'])->name('home');

// Rutas de autenticación (login movido a /panel/auth)
require __DIR__.'/auth.php';

// Rutas protegidas - requieren autenticación
Route::middleware(['auth', 'throttle:120,1'])->group(function () { // 120 peticiones por minuto para usuarios autenticados
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rutas de solo lectura para todos los roles
    Route::get('/trabajos/{trabajo}/detalle-venta', [TrabajoController::class, 'detalleVenta'])->name('trabajos.detalle-venta');

    // Rutas AJAX (accesibles para todos los usuarios autenticados) - Rate limit más estricto
    Route::post('/trabajos/buscar-cliente', [TrabajoController::class, 'buscarCliente'])
        ->middleware('throttle:30,1') // 30 búsquedas por minuto
        ->name('trabajos.buscar-cliente');
    Route::get('/servicios/{servicio}/piezas', [ServicioController::class, 'getPiezas'])
        ->middleware('throttle:30,1')
        ->name('servicios.piezas');

    // Rutas de perfil y cambio de contraseña (todos los usuarios autenticados)
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Rutas de administración - Solo Admin
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('cargos', CargoController::class);
        Route::resource('servicios', ServicioController::class);
        Route::resource('empleados', EmpleadoController::class);
        Route::resource('proveedores', ProveedorController::class)->parameters([
            'proveedores' => 'proveedor'
        ]);
        Route::resource('inventarios', InventarioController::class);
        Route::resource('users', UserController::class);
        Route::resource('activity-logs', ActivityLogController::class)->only(['index', 'show']);
    });

    // Rutas de cajero - Admin y Cajero
    Route::middleware(['role:admin,cajero'])->group(function () {
        Route::resource('clientes', ClienteController::class);
        Route::resource('trabajos', TrabajoController::class);
        Route::resource('gastos', GastoTallerController::class);
        
        // Rutas de pagos
        Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
        Route::get('/pagos/agrupado', [PagoController::class, 'indexAgrupado'])->name('pagos.index-agrupado');
        Route::post('/pagos/registrar', [PagoController::class, 'registrarPago'])->name('pagos.registrar');
        Route::post('/pagos/saldo', [PagoController::class, 'pagarSaldo'])->name('pagos.pagar-saldo');
        Route::get('/pagos/exportar-pdf', [PagoController::class, 'exportarPdf'])->name('pagos.exportar-pdf');
        Route::get('/pagos/exportar-pdf-agrupado', [PagoController::class, 'exportarPdfAgrupado'])->name('pagos.exportar-pdf-agrupado');
    });

    // Rutas de técnico - Solo consulta de trabajos asignados
    Route::middleware(['role:tecnico'])->group(function () {
        Route::get('/mis-trabajos', [TrabajoController::class, 'misTrabajosIndex'])->name('mis-trabajos.index');
        Route::get('/mis-trabajos/{trabajo}', [TrabajoController::class, 'show'])->name('mis-trabajos.show');
    });
});