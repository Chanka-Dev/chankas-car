<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,cajero')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cliente::withCount('trabajos');
        
        // Búsqueda por placa o teléfono
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('placas', 'like', '%' . $buscar . '%')
                  ->orWhere('telefono', 'like', '%' . $buscar . '%');
            });
        }
        
        // Ordenar por más recientes primero
        $query->orderBy('created_at', 'desc');
        
        // Paginar
        $clientes = $query->paginate(50)->withQueryString();
        
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'placas' => 'required|string|max:20',
            'telefono' => 'nullable|string|max:20',
        ]);

        Cliente::create($request->all());

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'placas' => 'required|string|max:20',
            'telefono' => 'nullable|string|max:20',
        ]);

        $cliente->update($request->all());

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        // Verificar si tiene trabajos asociados
        $cantidadTrabajos = $cliente->trabajos()->count();
        
        if ($cantidadTrabajos > 0) {
            return redirect()->route('clientes.index')
                ->with('error', "No se puede eliminar el cliente con placa '{$cliente->placas}' porque tiene {$cantidadTrabajos} trabajo(s) asociado(s). Por seguridad, los clientes con historial no pueden eliminarse.");
        }

        try {
            $cliente->delete();
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')
                ->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }
}