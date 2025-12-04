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

    public function index()
    {
        // Obtener gastos del taller
        $gastosTaller = GastoTaller::with('empleado')
            ->get()
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

        // Obtener pagos a técnicos
        $pagosTecnicos = \App\Models\PagoTecnico::with('empleado')
            ->get()
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
        $gastos = $gastosTaller->concat($pagosTecnicos)
            ->sortByDesc('fecha')
            ->values();

        return view('gastos.index', compact('gastos'));
    }

    public function create()
    {
        $empleados = Empleado::orderBy('nombre')->get();
        $empleadoActual = auth()->user()->id_empleado;
        
        // Obtener conceptos únicos de gastos existentes
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
        $empleados = Empleado::orderBy('nombre')->get();
        $empleadoActual = auth()->user()->id_empleado;
        
        // Obtener conceptos únicos de gastos existentes
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