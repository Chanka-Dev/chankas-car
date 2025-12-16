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
        $empleados = Empleado::with('cargo')->orderBy('nombre')->get();
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
            'ci' => [
                'required',
                'string',
                'max:20',
                'unique:empleados,ci',
                'regex:/^[0-9]+$/', // Solo números
            ],
            'nombre' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', // Solo letras y espacios (español)
            ],
            'apellido' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s()]+$/',
            ],
            'id_cargo' => 'required|exists:cargos,id_cargo',
        ], [
            'ci.regex' => 'El CI solo puede contener números.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',
            'telefono.regex' => 'El teléfono solo puede contener números y los símbolos: + - ( ) espacios.',
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
            'ci' => [
                'required',
                'string',
                'max:20',
                'unique:empleados,ci,' . $empleado->id_empleado . ',id_empleado',
                'regex:/^[0-9]+$/',
            ],
            'nombre' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            ],
            'apellido' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s()]+$/',
            ],
            'id_cargo' => 'required|exists:cargos,id_cargo',
        ], [
            'ci.regex' => 'El CI solo puede contener números.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',
            'telefono.regex' => 'El teléfono solo puede contener números y los símbolos: + - ( ) espacios.',
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
        $cantidadPagos = \DB::table('pagos_tecnicos')
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