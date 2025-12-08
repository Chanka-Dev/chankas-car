<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use App\Models\Empleado;
use App\Models\PagoTecnico;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,cajero')->except(['index']);
    }

    /**
     * Mostrar formulario de filtros y resultados
     */
    public function index(Request $request)
    {
        $empleados = Empleado::orderBy('nombre')->get();
        
        $trabajos = collect();
        $comisionesPeriodo = 0;
        $pagosPeriodo = 0;
        $saldoPendiente = 0;
        $saldoAnterior = 0;
        $saldoFinal = 0;
        $empleadoSeleccionado = null;
        $fechaInicio = null;
        $fechaFin = null;

        // Si hay filtros aplicados
        if ($request->has('id_empleado') && $request->id_empleado) {
            $request->validate([
                'id_empleado' => 'required|exists:empleados,id_empleado',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            ]);

            $empleadoSeleccionado = Empleado::find($request->id_empleado);
            $fechaInicio = $request->fecha_inicio;
            $fechaFin = $request->fecha_fin;

            // 1. SALDO ANTERIOR (Todo lo que pasó ANTES del período)
            $comisionesAnteriores = DB::table('trabajo_servicios')
                ->join('trabajos', 'trabajo_servicios.id_trabajo', '=', 'trabajos.id_trabajo')
                ->where('trabajos.id_empleado', $request->id_empleado)
                ->where('trabajos.fecha_trabajo', '<', $fechaInicio)
                ->sum('trabajo_servicios.importe_tecnico');

            $pagosAnteriores = PagoTecnico::where('id_empleado', $request->id_empleado)
                ->where('fecha_pago', '<', $fechaInicio)
                ->sum('monto_pagado');

            $saldoAnterior = $comisionesAnteriores - $pagosAnteriores;

            // 2. COMISIONES DEL PERÍODO (trabajo realizado en este rango)
            $trabajos = Trabajo::with(['cliente', 'trabajoServicios.servicio'])
                ->where('id_empleado', $request->id_empleado)
                ->whereBetween('fecha_trabajo', [$fechaInicio, $fechaFin])
                ->orderBy('fecha_trabajo', 'asc')
                ->get();

            $comisionesPeriodo = $trabajos->sum(function($trabajo) {
                return $trabajo->total_tecnico;
            });

            // 3. PAGOS DEL PERÍODO (dinero entregado en este rango)
            $pagosPeriodo = PagoTecnico::where('id_empleado', $request->id_empleado)
                ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                ->sum('monto_pagado');

            // 4. SALDO FINAL
            // Saldo Final = Saldo que traía + Lo que trabajó - Lo que se le pagó
            $saldoFinal = $saldoAnterior + $comisionesPeriodo - $pagosPeriodo;
        }

        // Obtener todos los saldos pendientes (para todos los empleados)
        $saldosPendientes = $this->calcularSaldosPendientes();

        // Obtener historial de pagos con paginación
        $historialPagos = PagoTecnico::with('empleado')
            ->orderBy('fecha_pago', 'desc')
            ->paginate(20);

        // Estadísticas de historial
        $totalPagosRealizados = PagoTecnico::sum('monto_pagado');
        $pagosMesActual = PagoTecnico::whereYear('fecha_pago', now()->year)
            ->whereMonth('fecha_pago', now()->month)
            ->sum('monto_pagado');

        return view('pagos.index', compact(
            'empleados',
            'trabajos',
            'comisionesPeriodo',
            'pagosPeriodo',
            'saldoAnterior',
            'saldoFinal',
            'empleadoSeleccionado',
            'fechaInicio',
            'fechaFin',
            'saldosPendientes',
            'historialPagos',
            'totalPagosRealizados',
            'pagosMesActual'
        ));
    }

    /**
     * Registrar un pago
     */
    public function registrarPago(Request $request)
    {
        $request->validate([
            'id_empleado' => 'required|exists:empleados,id_empleado',
            'fecha_pago' => 'required|date',
            'monto_pagado' => 'required|numeric|min:0.01',
            'periodo_inicio' => 'required|date',
            'periodo_fin' => 'required|date|after_or_equal:periodo_inicio',
            'tipo_pago' => 'required|in:completo,parcial,saldo',
            'observaciones' => 'nullable|string',
        ]);

        PagoTecnico::create($request->all());

        return redirect()->route('pagos.index')
            ->with('success', 'Pago registrado exitosamente.');
    }

    /**
     * Pagar saldo pendiente específico
     */
    public function pagarSaldo(Request $request)
    {
        $request->validate([
            'id_empleado' => 'required|exists:empleados,id_empleado',
            'monto_pagado' => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string',
        ]);

        // Obtener el saldo actual del empleado
        $saldoPendiente = $this->calcularSaldoEmpleado($request->id_empleado);

        if ($request->monto_pagado > $saldoPendiente) {
            return redirect()->back()
                ->with('error', 'El monto a pagar no puede ser mayor que el saldo pendiente.');
        }

        // Registrar el pago de saldo
        PagoTecnico::create([
            'id_empleado' => $request->id_empleado,
            'fecha_pago' => now(),
            'monto_pagado' => $request->monto_pagado,
            'periodo_inicio' => now()->subMonths(6), // Período arbitrario para saldos
            'periodo_fin' => now(),
            'tipo_pago' => 'saldo',
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('pagos.index')
            ->with('success', 'Saldo pagado exitosamente.');
    }

    /**
     * Calcular saldos pendientes de todos los empleados
     */
    private function calcularSaldosPendientes()
    {
        $empleados = Empleado::all();
        $saldos = [];

        foreach ($empleados as $empleado) {
            $saldo = $this->calcularSaldoEmpleado($empleado->id_empleado);
            
            if ($saldo > 0) {
                $saldos[] = [
                    'empleado' => $empleado,
                    'saldo' => $saldo,
                ];
            }
        }

        return collect($saldos);
    }

    /**
     * Calcular saldo pendiente de un empleado específico
     */
    private function calcularSaldoEmpleado($idEmpleado)
    {
        // Total de comisiones generadas (optimizado con join)
        $totalComisiones = DB::table('trabajo_servicios')
            ->join('trabajos', 'trabajo_servicios.id_trabajo', '=', 'trabajos.id_trabajo')
            ->where('trabajos.id_empleado', $idEmpleado)
            ->sum('trabajo_servicios.importe_tecnico');

        // Total pagado
        $totalPagado = PagoTecnico::where('id_empleado', $idEmpleado)
            ->sum('monto_pagado');

        return $totalComisiones - $totalPagado;
    }

    /**
     * Exportar a PDF
     */
    public function exportarPdf(Request $request)
    {
        $request->validate([
            'id_empleado' => 'required|exists:empleados,id_empleado',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $empleado = Empleado::with('cargo')->find($request->id_empleado);
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        // Obtener trabajos del empleado en el rango de fechas
        $trabajos = Trabajo::with(['cliente', 'trabajoServicios.servicio'])
            ->where('id_empleado', $request->id_empleado)
            ->whereBetween('fecha_trabajo', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_trabajo', 'asc')
            ->get();

        // Agrupar por fecha
        $trabajosPorFecha = $trabajos->groupBy(function($trabajo) {
            return $trabajo->fecha_trabajo->format('Y-m-d');
        });

        $totalComision = $trabajos->sum(function($trabajo) {
            return $trabajo->total_tecnico;
        });

        // Generar PDF
        $pdf = Pdf::loadView('pagos.pdf', compact(
            'empleado',
            'fechaInicio',
            'fechaFin',
            'trabajosPorFecha',
            'totalComision'
        ));

        $nombreArchivo = 'pago_' . $empleado->nombre . '_' . $empleado->apellido . '_' . date('Y-m-d') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    /**
     * Mostrar vista agrupada por tipo de servicio
     */
    public function indexAgrupado(Request $request)
    {
        $empleados = Empleado::orderBy('nombre')->get();
        
        $trabajos = collect();
        $comisionesPeriodo = 0;
        $pagosPeriodo = 0;
        $saldoAnterior = 0;
        $saldoFinal = 0;
        $empleadoSeleccionado = null;
        $fechaInicio = null;
        $fechaFin = null;
        $serviciosPorFecha = [];

        // Si hay filtros aplicados
        if ($request->has('id_empleado') && $request->id_empleado) {
            $request->validate([
                'id_empleado' => 'required|exists:empleados,id_empleado',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            ]);

            $empleadoSeleccionado = Empleado::find($request->id_empleado);
            $fechaInicio = $request->fecha_inicio;
            $fechaFin = $request->fecha_fin;

            // 1. SALDO ANTERIOR
            $comisionesAnteriores = DB::table('trabajo_servicios')
                ->join('trabajos', 'trabajo_servicios.id_trabajo', '=', 'trabajos.id_trabajo')
                ->where('trabajos.id_empleado', $request->id_empleado)
                ->where('trabajos.fecha_trabajo', '<', $fechaInicio)
                ->sum('trabajo_servicios.importe_tecnico');

            $pagosAnteriores = PagoTecnico::where('id_empleado', $request->id_empleado)
                ->where('fecha_pago', '<', $fechaInicio)
                ->sum('monto_pagado');

            $saldoAnterior = $comisionesAnteriores - $pagosAnteriores;

            // 2. TRABAJOS DEL PERÍODO
            $trabajos = Trabajo::with(['cliente', 'trabajoServicios.servicio'])
                ->where('id_empleado', $request->id_empleado)
                ->whereBetween('fecha_trabajo', [$fechaInicio, $fechaFin])
                ->orderBy('fecha_trabajo', 'asc')
                ->get();

            // Agrupar por fecha y luego por servicio
            $trabajosPorFecha = $trabajos->groupBy(function($trabajo) {
                return $trabajo->fecha_trabajo->format('Y-m-d');
            });

            foreach($trabajosPorFecha as $fecha => $trabajosDia) {
                $serviciosAgrupados = [];
                
                foreach($trabajosDia as $trabajo) {
                    foreach($trabajo->trabajoServicios as $ts) {
                        $nombreServicio = $ts->servicio->nombre;
                        
                        if(!isset($serviciosAgrupados[$nombreServicio])) {
                            $serviciosAgrupados[$nombreServicio] = [
                                'cantidad' => 0,
                                'total_tecnico' => 0
                            ];
                        }
                        
                        $serviciosAgrupados[$nombreServicio]['cantidad'] += $ts->cantidad;
                        $serviciosAgrupados[$nombreServicio]['total_tecnico'] += $ts->importe_tecnico;
                    }
                }
                
                $serviciosPorFecha[$fecha] = $serviciosAgrupados;
            }

            // 3. COMISIONES DEL PERÍODO
            $comisionesPeriodo = $trabajos->sum(function($trabajo) {
                return $trabajo->total_tecnico;
            });

            // 4. PAGOS DEL PERÍODO
            $pagosPeriodo = PagoTecnico::where('id_empleado', $request->id_empleado)
                ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                ->sum('monto_pagado');

            // 5. SALDO FINAL
            $saldoFinal = $saldoAnterior + $comisionesPeriodo - $pagosPeriodo;
        }

        // Obtener todos los saldos pendientes
        $saldosPendientes = $this->calcularSaldosPendientes();

        // Obtener historial de pagos con paginación
        $historialPagos = PagoTecnico::with('empleado')
            ->orderBy('fecha_pago', 'desc')
            ->paginate(20);

        // Estadísticas de historial
        $totalPagosRealizados = PagoTecnico::sum('monto_pagado');
        $pagosMesActual = PagoTecnico::whereYear('fecha_pago', now()->year)
            ->whereMonth('fecha_pago', now()->month)
            ->sum('monto_pagado');

        return view('pagos.index-agrupado', compact(
            'empleados',
            'trabajos',
            'serviciosPorFecha',
            'comisionesPeriodo',
            'pagosPeriodo',
            'saldoFinal',
            'saldoAnterior',
            'empleadoSeleccionado',
            'fechaInicio',
            'fechaFin',
            'saldosPendientes',
            'historialPagos',
            'totalPagosRealizados',
            'pagosMesActual'
        ));
    }

    /**
     * Exportar a PDF con vista agrupada por tipo de servicio
     */
    public function exportarPdfAgrupado(Request $request)
    {
        $request->validate([
            'id_empleado' => 'required|exists:empleados,id_empleado',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $empleado = Empleado::with('cargo')->find($request->id_empleado);
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        // Obtener trabajos del empleado en el rango de fechas
        $trabajos = Trabajo::with(['cliente', 'trabajoServicios.servicio'])
            ->where('id_empleado', $request->id_empleado)
            ->whereBetween('fecha_trabajo', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_trabajo', 'asc')
            ->get();

        // Agrupar servicios por fecha y tipo
        $serviciosPorFecha = [];
        
        foreach ($trabajos as $trabajo) {
            $fecha = $trabajo->fecha_trabajo->format('Y-m-d');
            
            if (!isset($serviciosPorFecha[$fecha])) {
                $serviciosPorFecha[$fecha] = [];
            }
            
            foreach ($trabajo->trabajoServicios as $ts) {
                $nombreServicio = $ts->servicio ? $ts->servicio->nombre : 'Servicio no encontrado';
                
                if (!isset($serviciosPorFecha[$fecha][$nombreServicio])) {
                    $serviciosPorFecha[$fecha][$nombreServicio] = [
                        'cantidad' => 0,
                        'total_tecnico' => 0
                    ];
                }
                
                $serviciosPorFecha[$fecha][$nombreServicio]['cantidad'] += 1;
                $serviciosPorFecha[$fecha][$nombreServicio]['total_tecnico'] += $ts->importe_tecnico;
            }
        }

        $totalComision = $trabajos->sum(function($trabajo) {
            return $trabajo->total_tecnico;
        });

        // Generar PDF
        $pdf = Pdf::loadView('pagos.pdf-agrupado', compact(
            'empleado',
            'fechaInicio',
            'fechaFin',
            'serviciosPorFecha',
            'totalComision'
        ));

        $nombreArchivo = 'pago_agrupado_' . $empleado->nombre . '_' . $empleado->apellido . '_' . date('Y-m-d') . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}