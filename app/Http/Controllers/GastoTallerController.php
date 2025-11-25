<?php

namespace App\Http\Controllers;

use App\Models\GastoTaller;
use App\Models\Empleado;
use Illuminate\Http\Request;

class GastoTallerController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,cajero')->except(['index', 'show']);
    }

    public function index()
    {
        $gastos = GastoTaller::with('empleado')->orderBy('fecha', 'desc')->get();
        return view('gastos.index', compact('gastos'));
    }

    public function create()
    {
        $empleados = Empleado::orderBy('nombre')->get();
        return view('gastos.create', compact('empleados'));
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
        return view('gastos.edit', compact('gasto', 'empleados'));
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