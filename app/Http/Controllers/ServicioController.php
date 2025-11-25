<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Inventario;
use App\Models\ServicioInventario;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except(['index', 'show', 'getPiezas']);
    }

    public function index()
    {
        $servicios = Servicio::all();
        return view('servicios.index', compact('servicios'));
    }

    public function create()
    {
        return view('servicios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'costo' => 'required|numeric|min:0',
            'comision' => 'required|numeric|min:0',
        ]);

        Servicio::create($request->all());

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio creado exitosamente.');
    }

    public function show(Servicio $servicio)
    {
        return view('servicios.show', compact('servicio'));
    }

    public function edit(Servicio $servicio)
    {
        $servicio->load('servicioInventarios.inventario');
        $inventarios = Inventario::orderBy('nombre')->get();
        return view('servicios.edit', compact('servicio', 'inventarios'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'costo' => 'required|numeric|min:0',
            'comision' => 'required|numeric|min:0',
            'piezas' => 'nullable|array',
            'piezas.*.id_inventario' => 'required|exists:inventario,id_inventario',
            'piezas.*.cantidad_base' => 'required|integer|min:1',
            'piezas.*.es_opcional' => 'nullable|boolean',
        ]);

        $servicio->update($request->only(['nombre', 'costo', 'comision']));

        // Actualizar piezas asociadas
        if ($request->has('piezas')) {
            // Eliminar piezas anteriores
            $servicio->servicioInventarios()->delete();

            // Agregar nuevas piezas
            foreach ($request->piezas as $pieza) {
                ServicioInventario::create([
                    'id_servicio' => $servicio->id_servicio,
                    'id_inventario' => $pieza['id_inventario'],
                    'cantidad_base' => $pieza['cantidad_base'],
                    'es_opcional' => $pieza['es_opcional'] ?? false,
                ]);
            }
        } else {
            // Si no hay piezas, eliminar todas las asociaciones
            $servicio->servicioInventarios()->delete();
        }

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio actualizado exitosamente.');
    }

    public function destroy(Servicio $servicio)
    {
        try {
            $servicio->delete();
            return redirect()->route('servicios.index')
                ->with('success', 'Servicio eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('servicios.index')
                ->with('error', 'No se puede eliminar el servicio porque tiene trabajos asociados.');
        }
    }

    public function getPiezas(Servicio $servicio)
    {
        $piezas = $servicio->servicioInventarios()->with('inventario')->get()->map(function($si) {
            return [
                'id_inventario' => $si->id_inventario,
                'nombre' => $si->inventario->nombre,
                'cantidad_base' => $si->cantidad_base,
                'es_opcional' => $si->es_opcional,
            ];
        });

        return response()->json(['piezas' => $piezas]);
    }
}