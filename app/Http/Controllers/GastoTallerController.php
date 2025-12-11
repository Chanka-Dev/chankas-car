<?php

namespace App\Http\Controllers;

use App\Models\GastoTaller;
use App\Models\Empleado;
use Illuminate\Http\Request;

class GastoTallerController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,cajero');
    }

    public function index(Request $request)
    {
        // Inicializar query para gastos del taller
        $queryGastos = GastoTaller::with('empleado');
        
        // Aplicar filtros
        if ($request->filled('fecha_desde')) {
            $queryGastos->where('fecha', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $queryGastos->where('fecha', '<=', $request->fecha_hasta);
        }
        
        if ($request->filled('concepto')) {
            $queryGastos->where('concepto', 'like', '%' . $request->concepto . '%');
        }
        
        // Obtener gastos del taller
        $gastosTaller = $queryGastos->get()
            ->map(function($gasto) {
                return [
                    'id' => $gasto->id_gasto,
                    'fecha' => $gasto->fecha,
                    'tipo' => 'gasto_taller',
                    'concepto' => $gasto->concepto,
                    'descripcion' => $gasto->descripcion,
                    'monto' => $gasto->monto,
                    'empleado' => $gasto->empleado,
                    'comprobante' => $gasto->comprobante,
                    'registro' => $gasto,
                ];
            });

        // Inicializar query para pagos a técnicos
        $queryPagos = \App\Models\PagoTecnico::with('empleado');
        
        // Aplicar filtros a pagos
        if ($request->filled('fecha_desde')) {
            $queryPagos->where('fecha_pago', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $queryPagos->where('fecha_pago', '<=', $request->fecha_hasta);
        }

        // Obtener pagos a técnicos
        $pagosTecnicos = $queryPagos->get()
            ->map(function($pago) {
                return [
                    'id' => $pago->id_pago,
                    'fecha' => $pago->fecha_pago,
                    'tipo' => 'pago_tecnico',
                    'concepto' => 'Pago a Técnico: ' . $pago->empleado->nombre . ' ' . $pago->empleado->apellido,
                    'descripcion' => 'Periodo: ' . $pago->periodo_inicio->format('d/m/Y') . ' - ' . $pago->periodo_fin->format('d/m/Y') . ($pago->observaciones ? ' | ' . $pago->observaciones : ''),
                    'monto' => $pago->monto_pagado,
                    'empleado' => $pago->empleado,
                    'comprobante' => null,
                    'registro' => $pago,
                ];
            });

        // Combinar y ordenar por fecha descendente
        $gastosCollection = $gastosTaller->concat($pagosTecnicos)
            ->sortByDesc('fecha')
            ->values();

        // Paginación manual
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $perPage = 30;
        $currentPageItems = $gastosCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $gastos = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $gastosCollection->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        
        // Mantener los parámetros de búsqueda en la paginación
        $gastos->appends($request->all());

        return view('gastos.index', compact('gastos'));
    }

    public function create()
    {
        // Solo campos necesarios para el select
        $empleados = Empleado::select('id_empleado', 'nombre', 'apellido')
            ->orderBy('nombre')
            ->get();
        $empleadoActual = auth()->user()->id_empleado;
        
        // Obtener conceptos únicos de gastos existentes (ya optimizado)
        $conceptos = GastoTaller::select('concepto')
            ->distinct()
            ->orderBy('concepto')
            ->pluck('concepto');
        
        return view('gastos.create', compact('empleados', 'empleadoActual', 'conceptos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'concepto' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'monto' => 'required|numeric|min:0',
            'comprobante' => 'nullable|string|max:100',
            'id_empleado' => 'nullable|exists:empleados,id_empleado',
        ]);

        GastoTaller::create($request->all());

        return redirect()->route('gastos.index')
            ->with('success', 'Gasto registrado exitosamente.');
    }

    public function show(GastoTaller $gasto)
    {
        return view('gastos.show', compact('gasto'));
    }

    public function edit(GastoTaller $gasto)
    {
        // Solo campos necesarios
        $empleados = Empleado::select('id_empleado', 'nombre', 'apellido')
            ->orderBy('nombre')
            ->get();
        $empleadoActual = auth()->user()->id_empleado;
        
        // Obtener conceptos únicos de gastos existentes (ya optimizado)
        $conceptos = GastoTaller::select('concepto')
            ->distinct()
            ->orderBy('concepto')
            ->pluck('concepto');
        
        return view('gastos.edit', compact('gasto', 'empleados', 'empleadoActual', 'conceptos'));
    }

    public function update(Request $request, GastoTaller $gasto)
    {
        $request->validate([
            'fecha' => 'required|date',
            'concepto' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'monto' => 'required|numeric|min:0',
            'comprobante' => 'nullable|string|max:100',
            'id_empleado' => 'nullable|exists:empleados,id_empleado',
        ]);

        $gasto->update($request->all());

        return redirect()->route('gastos.index')
            ->with('success', 'Gasto actualizado exitosamente.');
    }

    public function destroy(GastoTaller $gasto)
    {
        $gasto->delete();
        return redirect()->route('gastos.index')
            ->with('success', 'Gasto eliminado exitosamente.');
    }
}