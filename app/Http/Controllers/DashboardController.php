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

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $totalTrabajos = Trabajo::count();
        $totalEmpleados = Empleado::count();
        $totalClientes = Cliente::count();
        $totalServicios = Servicio::count();

        // Trabajos del mes actual
        $trabajosMesActual = Trabajo::whereMonth('fecha_trabajo', date('m'))
            ->whereYear('fecha_trabajo', date('Y'))
            ->count();

        // Ingresos del mes actual (suma de todos los servicios)
        $trabajosMes = Trabajo::with('trabajoServicios')
            ->whereMonth('fecha_trabajo', date('m'))
            ->whereYear('fecha_trabajo', date('Y'))
            ->get();

        $ingresosMesActual = $trabajosMes->sum(function($trabajo) {
            return $trabajo->total_cliente;
        });

        // Comisiones del mes actual
        $comisionesMesActual = $trabajosMes->sum(function($trabajo) {
            return $trabajo->total_tecnico;
        });

        // Gastos del mes actual
        $gastosMesActual = GastoTaller::whereMonth('fecha', date('m'))
            ->whereYear('fecha', date('Y'))
            ->sum('monto');

        // Utilidad neta del mes
        $utilidadNeta = $ingresosMesActual - $comisionesMesActual - $gastosMesActual;

        // Últimos 10 trabajos
        $ultimosTrabajos = Trabajo::with(['empleado', 'cliente', 'trabajoServicios.servicio'])
            ->orderBy('fecha_trabajo', 'desc')
            ->limit(10)
            ->get();

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

        // Alertas de inventario bajo
        $itemsBajoStock = Inventario::whereRaw('stock_actual <= stock_minimo')
            ->orderBy('stock_actual', 'asc')
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

        return view('dashboard', compact(
            'totalTrabajos',
            'totalEmpleados',
            'totalClientes',
            'totalServicios',
            'trabajosMesActual',
            'ingresosMesActual',
            'comisionesMesActual',
            'gastosMesActual',
            'utilidadNeta',
            'ultimosTrabajos',
            'ingresosPorServicio',
            'serviciosMasSolicitados',
            'itemsBajoStock',
            'gastosPorConcepto'
        ));
    }
}