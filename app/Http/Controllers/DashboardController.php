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
    public function index()
    {
        // Caché de 5 minutos para estadísticas generales (raramente cambian)
        $estadisticas = Cache::remember('dashboard_estadisticas_generales', 300, function () {
            return [
                'totalTrabajos' => Trabajo::count(),
                'totalEmpleados' => Empleado::count(),
                'totalClientes' => Cliente::count(),
                'totalServicios' => Servicio::count(),
            ];
        });

        // Caché de 2 minutos para datos del mes actual
        $datosMes = Cache::remember('dashboard_datos_mes_' . date('Y-m'), 120, function () {
            // Trabajos del mes actual
            $trabajosMesActual = Trabajo::whereMonth('fecha_trabajo', date('m'))
                ->whereYear('fecha_trabajo', date('Y'))
                ->count();

            // Ingresos y comisiones del mes con una sola query optimizada
            $totalesMes = Trabajo::whereMonth('fecha_trabajo', date('m'))
                ->whereYear('fecha_trabajo', date('Y'))
                ->selectRaw('
                    SUM(total_cliente) as ingresos_total,
                    SUM(total_tecnico) as comisiones_total
                ')
                ->first();

            // Gastos del mes actual
            $gastosMesActual = GastoTaller::whereMonth('fecha', date('m'))
                ->whereYear('fecha', date('Y'))
                ->sum('monto');

            return [
                'trabajosMesActual' => $trabajosMesActual,
                'ingresosMesActual' => $totalesMes->ingresos_total ?? 0,
                'comisionesMesActual' => $totalesMes->comisiones_total ?? 0,
                'gastosMesActual' => $gastosMesActual,
                'utilidadNeta' => ($totalesMes->ingresos_total ?? 0) - ($totalesMes->comisiones_total ?? 0) - $gastosMesActual,
            ];
        });

        // Sin caché: últimos trabajos (siempre actualizados)
        $ultimosTrabajos = Trabajo::with(['empleado:id_empleado,nombre,apellido', 'cliente:id_cliente,placas'])
            ->select('id_trabajo', 'fecha_trabajo', 'id_empleado', 'id_cliente', 'total_cliente', 'total_tecnico')
            ->orderBy('fecha_trabajo', 'desc')
            ->limit(10)
            ->get();

        // Caché de 5 minutos para reportes del mes
        $reportesMes = Cache::remember('dashboard_reportes_mes_' . date('Y-m'), 300, function () {
            // Ingresos por tipo de servicio (del mes)
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
                ->whereMonth('trabajos.fecha_trabajo', date('m'))
                ->whereYear('trabajos.fecha_trabajo', date('Y'))
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
                ->whereMonth('trabajos.fecha_trabajo', date('m'))
                ->whereYear('trabajos.fecha_trabajo', date('Y'))
                ->groupBy('servicios.id_servicio', 'servicios.nombre')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get();

            // Gastos por concepto (del mes)
            $gastosPorConcepto = GastoTaller::select('concepto', DB::raw('SUM(monto) as total'))
                ->whereMonth('fecha', date('m'))
                ->whereYear('fecha', date('Y'))
                ->groupBy('concepto')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get();

            return compact('ingresosPorServicio', 'serviciosMasSolicitados', 'gastosPorConcepto');
        });

        // Sin caché: alertas de stock bajo (crítico, debe estar actualizado)
        $itemsBajoStock = Inventario::whereRaw('stock_actual <= stock_minimo')
            ->select('id_inventario', 'nombre', 'stock_actual', 'stock_minimo', 'unidad_medida')
            ->orderBy('stock_actual', 'asc')
            ->limit(5)
            ->get();

        return view('dashboard', array_merge(
            $estadisticas,
            $datosMes,
            $reportesMes,
            compact('ultimosTrabajos', 'itemsBajoStock')
        ));
    }
}