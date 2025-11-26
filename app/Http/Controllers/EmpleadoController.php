<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Cargo;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $empleados = Empleado::with('cargo')->get();
        return view('empleados.index', compact('empleados'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cargos = Cargo::all();
        return view('empleados.create', compact('cargos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ci' => 'required|string|max:20|unique:empleados,ci',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'id_cargo' => 'required|exists:cargos,id_cargo',
        ]);

        Empleado::create($request->all());

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Empleado $empleado)
    {
        return view('empleados.show', compact('empleado'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empleado $empleado)
    {
        $cargos = Cargo::all();
        return view('empleados.edit', compact('empleado', 'cargos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'ci' => 'required|string|max:20|unique:empleados,ci,' . $empleado->id_empleado . ',id_empleado',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'id_cargo' => 'required|exists:cargos,id_cargo',
        ]);

        $empleado->update($request->all());

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empleado $empleado)
    {
        // Verificar si tiene trabajos asociados
        $cantidadTrabajos = $empleado->trabajos()->count();
        
        if ($cantidadTrabajos > 0) {
            return redirect()->route('empleados.index')
                ->with('error', "No se puede eliminar al empleado '{$empleado->nombre} {$empleado->apellido}' porque tiene {$cantidadTrabajos} trabajo(s) asociado(s). Por seguridad, los técnicos con historial no pueden eliminarse.");
        }

        // Verificar si tiene pagos registrados
        $cantidadPagos = \DB::table('pagos')
            ->where('id_empleado', $empleado->id_empleado)
            ->count();
        
        if ($cantidadPagos > 0) {
            return redirect()->route('empleados.index')
                ->with('error', "No se puede eliminar al empleado '{$empleado->nombre} {$empleado->apellido}' porque tiene {$cantidadPagos} pago(s) registrado(s). Por seguridad, los técnicos con historial no pueden eliminarse.");
        }

        try {
            $empleado->delete();
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('empleados.index')
                ->with('error', 'Error al eliminar el empleado: ' . $e->getMessage());
        }
    }
}