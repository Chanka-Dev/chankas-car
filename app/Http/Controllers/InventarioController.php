<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,cajero')->except(['index', 'show']);
    }

    public function index()
    {
        $inventarios = Inventario::with('proveedor')->get();
        return view('inventarios.index', compact('inventarios'));
    }

    public function create()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        return view('inventarios.create', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|in:unidad,metro,kilo,litro,caja,par',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'tipo_stock' => 'required|in:contable,pregunta',
            'id_proveedor' => 'nullable|exists:proveedores,id_proveedor',
            'fecha_ingreso' => 'nullable|date',
        ]);

        Inventario::create($request->all());

        return redirect()->route('inventarios.index')
            ->with('success', 'Item agregado al inventario exitosamente.');
    }

    public function show(Inventario $inventario)
    {
        return view('inventarios.show', compact('inventario'));
    }

    public function edit(Inventario $inventario)
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        return view('inventarios.edit', compact('inventario', 'proveedores'));
    }

    public function update(Request $request, Inventario $inventario)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|in:unidad,metro,kilo,litro,caja,par',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'tipo_stock' => 'required|in:contable,pregunta',
            'id_proveedor' => 'nullable|exists:proveedores,id_proveedor',
            'fecha_ingreso' => 'nullable|date',
        ]);

        $inventario->update($request->all());

        return redirect()->route('inventarios.index')
            ->with('success', 'Item actualizado exitosamente.');
    }

    public function destroy(Inventario $inventario)
    {
        $inventario->delete();
        return redirect()->route('inventarios.index')
            ->with('success', 'Item eliminado del inventario exitosamente.');
    }
}