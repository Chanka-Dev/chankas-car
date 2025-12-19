<?php

namespace App\Http\Controllers;

use App\Models\TipoGasto;
use Illuminate\Http\Request;

class TipoGastoController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TipoGasto::query();

        // Filtro por nombre
        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $tiposGastos = $query->orderBy('nombre')->paginate(20);
        $tiposGastos->appends($request->all());

        return view('tipos-gastos.index', compact('tiposGastos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tipos-gastos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150|unique:tipos_gastos,nombre',
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'required|boolean',
        ], [
            'nombre.required' => 'El nombre del tipo de gasto es obligatorio.',
            'nombre.unique' => 'Ya existe un tipo de gasto con este nombre.',
            'nombre.max' => 'El nombre no puede exceder 150 caracteres.',
        ]);

        TipoGasto::create($request->all());

        return redirect()->route('tipos-gastos.index')
            ->with('success', 'Tipo de gasto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoGasto $tiposGasto)
    {
        // Obtener gastos que usan este tipo
        $gastosRelacionados = \App\Models\GastoTaller::where('concepto', $tiposGasto->nombre)
            ->with('empleado')
            ->latest('fecha')
            ->limit(10)
            ->get();

        return view('tipos-gastos.show', compact('tiposGasto', 'gastosRelacionados'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoGasto $tiposGasto)
    {
        return view('tipos-gastos.edit', compact('tiposGasto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoGasto $tiposGasto)
    {
        $request->validate([
            'nombre' => 'required|string|max:150|unique:tipos_gastos,nombre,' . $tiposGasto->id_tipo_gasto . ',id_tipo_gasto',
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'required|boolean',
        ], [
            'nombre.required' => 'El nombre del tipo de gasto es obligatorio.',
            'nombre.unique' => 'Ya existe un tipo de gasto con este nombre.',
            'nombre.max' => 'El nombre no puede exceder 150 caracteres.',
        ]);

        $tiposGasto->update($request->all());

        return redirect()->route('tipos-gastos.index')
            ->with('success', 'Tipo de gasto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoGasto $tiposGasto)
    {
        // Verificar si tiene gastos asociados
        if ($tiposGasto->tieneGastos()) {
            return redirect()->route('tipos-gastos.index')
                ->with('error', 'No se puede eliminar este tipo de gasto porque tiene registros asociados. Puede desactivarlo en su lugar.');
        }

        $tiposGasto->delete();

        return redirect()->route('tipos-gastos.index')
            ->with('success', 'Tipo de gasto eliminado exitosamente.');
    }
}
