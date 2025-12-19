<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use App\Models\Empleado;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\GastoTaller;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Obtener rango de fechas del filtro o usar mes actual por defecto
        $fechaDesde = $request->input('fecha_desde', now()->startOfMonth()->format('Y-m-d'));
        $fechaHasta = $request->input('fecha_hasta', now()->format('Y-m-d'));
        
        // Caché de 5 minutos para estadísticas generales (raramente cambian)
        $estadisticas = Cache::remember('dashboard_estadisticas_generales', 300, function () {
            return [
                'totalTrabajos' => Trabajo::count(),
                'totalEmpleados' => Empleado::count(),
                'totalClientes' => Cliente::count(),
                'totalServicios' => Servicio::count(),
            ];
        });

        // Cache key único por rango de fechas
        $cacheKey = 'dashboard_datos_' . md5($fechaDesde . $fechaHasta);
        
        $datosPeriodo = Cache::remember($cacheKey, 120, function () use ($fechaDesde, $fechaHasta) {
            // Trabajos del periodo
            $trabajosPeriodo = Trabajo::whereBetween('fecha_trabajo', [$fechaDesde, $fechaHasta])
                ->count();

            // Ingresos y comisiones del periodo - desde trabajo_servicios
            $totalesPeriodo = DB::table('trabajo_servicios')
                ->join('trabajos', 'trabajo_servicios.id_trabajo', '=', 'trabajos.id_trabajo')
                ->whereBetween('trabajos.fecha_trabajo', [$fechaDesde, $fechaHasta])
                ->selectRaw('
                    SUM(trabajo_servicios.importe_cliente) as ingresos_total,
                    SUM(trabajo_servicios.importe_tecnico) as comisiones_total
                ')
                ->first();

            // Gastos del periodo
            $gastosPeriodo = GastoTaller::whereBetween('fecha', [$fechaDesde, $fechaHasta])
                ->sum('monto');

            return [
                'trabajosPeriodo' => $trabajosPeriodo,
                'ingresosPeriodo' => $totalesPeriodo->ingresos_total ?? 0,
                'comisionesPeriodo' => $totalesPeriodo->comisiones_total ?? 0,
                'gastosPeriodo' => $gastosPeriodo,
                'utilidadNeta' => ($totalesPeriodo->ingresos_total ?? 0) - ($totalesPeriodo->comisiones_total ?? 0) - $gastosPeriodo,
            ];
        });

        // Sin caché: últimos trabajos (siempre actualizados, no se filtra por fecha)
        $ultimosTrabajos = Trabajo::with(['empleado:id_empleado,nombre,apellido', 'cliente:id_cliente,placas'])
            ->select('id_trabajo', 'fecha_trabajo', 'id_empleado', 'id_cliente')
            ->orderBy('fecha_trabajo', 'desc')
            ->limit(10)
            ->get();

        // Reportes del periodo con caché
        $reportesPeriodo = Cache::remember($cacheKey . '_reportes', 300, function () use ($fechaDesde, $fechaHasta) {
            // Ingresos por tipo de servicio (del periodo)
            $ingresosPorServicio = DB::table('trabajo_servicios')
                ->join('trabajos', 'trabajo_servicios.id_trabajo', '=', 'trabajos.id_trabajo')
                ->join('servicios', 'trabajo_servicios.id_servicio', '=', 'servicios.id_servicio')
                ->select(
                    'servicios.id_servicio',
                    'servicios.nombre',
                    DB::raw('COUNT(*) as cantidad'),
                    DB::raw('SUM(trabajo_servicios.importe_cliente) as total_ingresos'),
                    DB::raw('SUM(trabajo_servicios.importe_tecnico) as total_comisiones'),
                    DB::raw('AVG(trabajo_servicios.importe_cliente) as promedio_ingreso')
                )
                ->whereBetween('trabajos.fecha_trabajo', [$fechaDesde, $fechaHasta])
                ->groupBy('servicios.id_servicio', 'servicios.nombre')
                ->orderBy('total_ingresos', 'desc')
                ->get();

            // Servicios más solicitados
            $serviciosMasSolicitados = DB::table('trabajo_servicios')
                ->join('trabajos', 'trabajo_servicios.id_trabajo', '=', 'trabajos.id_trabajo')
                ->join('servicios', 'trabajo_servicios.id_servicio', '=', 'servicios.id_servicio')
                ->select(
                    'servicios.id_servicio',
                    'servicios.nombre',
                    DB::raw('COUNT(*) as total')
                )
                ->whereBetween('trabajos.fecha_trabajo', [$fechaDesde, $fechaHasta])
                ->groupBy('servicios.id_servicio', 'servicios.nombre')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get();

            return compact('ingresosPorServicio', 'serviciosMasSolicitados');
        });

        // Sin caché: alertas de stock bajo (crítico, debe estar actualizado)
        $itemsBajoStock = Inventario::whereRaw('stock_actual <= stock_minimo')
            ->select('id_inventario', 'nombre', 'stock_actual', 'stock_minimo', 'unidad_medida')
            ->orderBy('stock_actual', 'asc')
            ->limit(5)
            ->get();

        return view('dashboard', array_merge(
            $estadisticas,
            $datosPeriodo,
            $reportesPeriodo,
            compact('ultimosTrabajos', 'itemsBajoStock', 'fechaDesde', 'fechaHasta')
        ));
    }
}