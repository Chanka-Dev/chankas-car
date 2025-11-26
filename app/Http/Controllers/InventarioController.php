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
            'nombre' => [
                'required',
                'string',
                'max:150',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.(),\/]+$/',
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.,:;()\n\r]+$/',
            ],
            'unidad_medida' => 'required|in:unidad,metro,kilo,litro,caja,par',
            'precio_compra' => 'required|numeric|min:0|max:999999.99',
            'precio_venta' => 'required|numeric|min:0|max:999999.99',
            'stock_actual' => 'required|integer|min:0|max:999999',
            'stock_minimo' => 'required|integer|min:0|max:9999',
            'tipo_stock' => 'required|in:contable,pregunta',
            'id_proveedor' => 'nullable|exists:proveedores,id_proveedor',
            'fecha_ingreso' => 'nullable|date|before_or_equal:today',
        ], [
            'nombre.regex' => 'El nombre del item contiene caracteres no permitidos.',
            'descripcion.regex' => 'La descripción contiene caracteres no permitidos.',
            'precio_compra.max' => 'El precio de compra no puede exceder 999,999.99 Bs.',
            'precio_venta.max' => 'El precio de venta no puede exceder 999,999.99 Bs.',
            'stock_actual.max' => 'El stock actual no puede exceder 999,999 unidades.',
            'stock_minimo.max' => 'El stock mínimo no puede exceder 9,999 unidades.',
            'fecha_ingreso.before_or_equal' => 'La fecha de ingreso no puede ser futura.',
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
            'nombre' => [
                'required',
                'string',
                'max:150',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.(),\/]+$/',
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.,:;()\n\r]+$/',
            ],
            'unidad_medida' => 'required|in:unidad,metro,kilo,litro,caja,par',
            'precio_compra' => 'required|numeric|min:0|max:999999.99',
            'precio_venta' => 'required|numeric|min:0|max:999999.99',
            'stock_actual' => 'required|integer|min:0|max:999999',
            'stock_minimo' => 'required|integer|min:0|max:9999',
            'tipo_stock' => 'required|in:contable,pregunta',
            'id_proveedor' => 'nullable|exists:proveedores,id_proveedor',
            'fecha_ingreso' => 'nullable|date|before_or_equal:today',
        ], [
            'nombre.regex' => 'El nombre del item contiene caracteres no permitidos.',
            'descripcion.regex' => 'La descripción contiene caracteres no permitidos.',
            'precio_compra.max' => 'El precio de compra no puede exceder 999,999.99 Bs.',
            'precio_venta.max' => 'El precio de venta no puede exceder 999,999.99 Bs.',
            'stock_actual.max' => 'El stock actual no puede exceder 999,999 unidades.',
            'stock_minimo.max' => 'El stock mínimo no puede exceder 9,999 unidades.',
            'fecha_ingreso.before_or_equal' => 'La fecha de ingreso no puede ser futura.',
        ]);

        $inventario->update($request->all());

        return redirect()->route('inventarios.index')
            ->with('success', 'Item actualizado exitosamente.');
    }

    public function destroy(Inventario $inventario)
    {
        // Verificar si está usado en trabajos
        $cantidadTrabajos = \DB::table('trabajo_inventario')
            ->where('id_inventario', $inventario->id_inventario)
            ->count();
        
        if ($cantidadTrabajos > 0) {
            return redirect()->route('inventarios.index')
                ->with('error', "No se puede eliminar '{$inventario->nombre}' porque está registrado en {$cantidadTrabajos} trabajo(s). Por seguridad, los items con historial de uso no pueden eliminarse.");
        }

        try {
            // Eliminar relaciones con servicios (estas sí se pueden eliminar)
            \DB::table('servicio_inventario')
                ->where('id_inventario', $inventario->id_inventario)
                ->delete();
            
            $inventario->delete();
            return redirect()->route('inventarios.index')
                ->with('success', 'Item eliminado del inventario exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('inventarios.index')
                ->with('error', 'Error al eliminar el item: ' . $e->getMessage());
        }
    }
}