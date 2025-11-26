<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use App\Models\Servicio;
use App\Models\Empleado;
use App\Models\Cliente;
use App\Models\TrabajoServicio;
use App\Models\TrabajoInventario;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TrabajoController extends Controller
{
    public function __construct()
    {
        // Solo usuarios con rol lectura no pueden crear/editar/eliminar
        $this->middleware('role:admin,cajero,tecnico')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = Trabajo::with(['empleado', 'cliente', 'trabajoServicios.servicio'])
            ->orderBy('fecha_trabajo', 'desc');
        
        // Búsqueda general (busca en placa, técnico, servicio, observaciones)
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            
            $query->where(function($q) use ($buscar) {
                // Buscar en placa del cliente
                $q->whereHas('cliente', function($subQ) use ($buscar) {
                    $subQ->where('placas', 'like', '%' . $buscar . '%')
                         ->orWhere('telefono', 'like', '%' . $buscar . '%');
                })
                // Buscar en nombre del técnico
                ->orWhereHas('empleado', function($subQ) use ($buscar) {
                    $subQ->where('nombre', 'like', '%' . $buscar . '%')
                         ->orWhere('apellido', 'like', '%' . $buscar . '%');
                })
                // Buscar en servicios realizados
                ->orWhereHas('trabajoServicios.servicio', function($subQ) use ($buscar) {
                    $subQ->where('nombre', 'like', '%' . $buscar . '%');
                })
                // Buscar en observaciones del trabajo
                ->orWhere('observaciones', 'like', '%' . $buscar . '%')
                // Buscar en observaciones de los servicios
                ->orWhereHas('trabajoServicios', function($subQ) use ($buscar) {
                    $subQ->where('observaciones', 'like', '%' . $buscar . '%');
                });
            });
        }
        
        // Filtro por técnico específico
        if ($request->filled('id_empleado')) {
            $query->where('id_empleado', $request->id_empleado);
        }
        
        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_trabajo', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_trabajo', '<=', $request->fecha_hasta);
        }
        
        // Paginar en lugar de get()
        $trabajos = $query->paginate(50)->withQueryString();
        
        // Obtener empleados para el filtro
        $empleados = \App\Models\Empleado::orderBy('nombre')->get();
        
        return view('trabajos.index', compact('trabajos', 'empleados'));
    }

    public function create()
    {
        $servicios = Servicio::all();
        $empleados = Empleado::with('cargo')->get();
        return view('trabajos.create', compact('servicios', 'empleados'));
    }

public function store(Request $request)
{
    $request->validate([
        'fecha_trabajo' => 'required|date',
        'fecha_recepcion' => 'required|date',
        'fecha_recalificacion' => 'nullable|date',
        'placas' => 'nullable|string|max:20',
        'telefono' => 'nullable|string|max:20',
        'id_empleado' => 'required|exists:empleados,id_empleado',
        'observaciones' => 'nullable|string',
        'servicios' => 'required|array|min:1',
        'servicios.*.id_servicio' => 'required|exists:servicios,id_servicio',
        'servicios.*.cantidad' => 'required|integer|min:1',
        'servicios.*.importe_cliente' => 'required|numeric|min:0',
        'servicios.*.importe_tecnico' => 'required|numeric|min:0',
        'servicios.*.observaciones' => 'nullable|string',
        'piezas' => 'nullable|array',
        'piezas.*.id_inventario' => 'required|exists:inventario,id_inventario',
        'piezas.*.cantidad_usada' => 'required|numeric|min:0.01',
        'piezas.*.precio_unitario' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();
    try {
        // Buscar o crear cliente
        $clienteId = null;
        
        if ($request->placas) {
            $cliente = Cliente::where('placas', strtoupper($request->placas))->first();
            
            if ($cliente) {
                if ($request->telefono && $cliente->telefono !== $request->telefono) {
                    $cliente->update(['telefono' => $request->telefono]);
                }
            } else {
                $cliente = Cliente::create([
                    'placas' => strtoupper($request->placas),
                    'telefono' => $request->telefono,
                ]);
            }
            $clienteId = $cliente->id_cliente;
        }

        // Crear el trabajo
        $trabajo = Trabajo::create([
            'fecha_trabajo' => $request->fecha_trabajo,
            'fecha_recepcion' => $request->fecha_recepcion,
            'fecha_recalificacion' => $request->fecha_recalificacion,
            'id_empleado' => $request->id_empleado,
            'id_cliente' => $clienteId,
            'observaciones' => $request->observaciones,
        ]);

        // Crear los servicios del trabajo
        foreach ($request->servicios as $servicio) {
            TrabajoServicio::create([
                'id_trabajo' => $trabajo->id_trabajo,
                'id_servicio' => $servicio['id_servicio'],
                'cantidad' => $servicio['cantidad'],
                'importe_cliente' => $servicio['importe_cliente'],
                'importe_tecnico' => $servicio['importe_tecnico'],
                'observaciones' => $servicio['observaciones'] ?? null,
            ]);
        }

        // Crear las piezas usadas y descontar del inventario
        if ($request->has('piezas') && is_array($request->piezas)) {
            foreach ($request->piezas as $pieza) {
                // Registrar pieza usada
                TrabajoInventario::create([
                    'id_trabajo' => $trabajo->id_trabajo,
                    'id_inventario' => $pieza['id_inventario'],
                    'cantidad_usada' => $pieza['cantidad_usada'],
                    'precio_unitario' => $pieza['precio_unitario'],
                ]);

                // Descontar del inventario si es tipo "contable"
                $item = Inventario::find($pieza['id_inventario']);
                if ($item && $item->tipo_stock === 'contable') {
                    $item->stock_actual -= $pieza['cantidad_usada'];
                    $item->save();
                }
            }
        }

        DB::commit();

        return redirect()->route('trabajos.index')
            ->with('success', 'Trabajo registrado exitosamente.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Error al registrar el trabajo: ' . $e->getMessage())
            ->withInput();
    }
}

public function update(Request $request, Trabajo $trabajo)
{
    $request->validate([
        'fecha_trabajo' => 'required|date',
        'fecha_recepcion' => 'required|date',
        'fecha_recalificacion' => 'nullable|date',
        'placas' => 'nullable|string|max:20',
        'telefono' => 'nullable|string|max:20',
        'id_empleado' => 'required|exists:empleados,id_empleado',
        'observaciones' => 'nullable|string',
        'servicios' => 'required|array|min:1',
        'servicios.*.id_servicio' => 'required|exists:servicios,id_servicio',
        'servicios.*.cantidad' => 'required|integer|min:1',
        'servicios.*.importe_cliente' => 'required|numeric|min:0',
        'servicios.*.importe_tecnico' => 'required|numeric|min:0',
        'servicios.*.observaciones' => 'nullable|string',
        'piezas' => 'nullable|array',
        'piezas.*.id_inventario' => 'required|exists:inventario,id_inventario',
        'piezas.*.cantidad_usada' => 'required|numeric|min:0.01',
        'piezas.*.precio_unitario' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();
    try {
        // Primero, devolver al inventario las piezas antiguas (solo las contables)
        foreach ($trabajo->trabajoInventarios as $ti) {
            $item = $ti->inventario;
            if ($item && $item->tipo_stock === 'contable') {
                $item->stock_actual += $ti->cantidad_usada;
                $item->save();
            }
        }

        // Buscar o crear cliente
        $clienteId = null;
        
        if ($request->placas) {
            $cliente = Cliente::where('placas', strtoupper($request->placas))->first();
            
            if ($cliente) {
                if ($request->telefono && $cliente->telefono !== $request->telefono) {
                    $cliente->update(['telefono' => $request->telefono]);
                }
            } else {
                $cliente = Cliente::create([
                    'placas' => strtoupper($request->placas),
                    'telefono' => $request->telefono,
                ]);
            }
            $clienteId = $cliente->id_cliente;
        }

        // Actualizar el trabajo
        $trabajo->update([
            'fecha_trabajo' => $request->fecha_trabajo,
            'fecha_recepcion' => $request->fecha_recepcion,
            'fecha_recalificacion' => $request->fecha_recalificacion,
            'id_empleado' => $request->id_empleado,
            'id_cliente' => $clienteId,
            'observaciones' => $request->observaciones,
        ]);

        // Eliminar servicios antiguos
        $trabajo->trabajoServicios()->delete();

        // Crear los nuevos servicios
        foreach ($request->servicios as $servicio) {
            TrabajoServicio::create([
                'id_trabajo' => $trabajo->id_trabajo,
                'id_servicio' => $servicio['id_servicio'],
                'cantidad' => $servicio['cantidad'],
                'importe_cliente' => $servicio['importe_cliente'],
                'importe_tecnico' => $servicio['importe_tecnico'],
                'observaciones' => $servicio['observaciones'] ?? null,
            ]);
        }

        // Eliminar piezas antiguas
        $trabajo->trabajoInventarios()->delete();

        // Crear las nuevas piezas usadas y descontar del inventario
        if ($request->has('piezas') && is_array($request->piezas)) {
            foreach ($request->piezas as $pieza) {
                // Registrar pieza usada
                TrabajoInventario::create([
                    'id_trabajo' => $trabajo->id_trabajo,
                    'id_inventario' => $pieza['id_inventario'],
                    'cantidad_usada' => $pieza['cantidad_usada'],
                    'precio_unitario' => $pieza['precio_unitario'],
                ]);

                // Descontar del inventario si es tipo "contable"
                $item = Inventario::find($pieza['id_inventario']);
                if ($item && $item->tipo_stock === 'contable') {
                    $item->stock_actual -= $pieza['cantidad_usada'];
                    $item->save();
                }
            }
        }

        DB::commit();

        return redirect()->route('trabajos.index')
            ->with('success', 'Trabajo actualizado exitosamente.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Error al actualizar el trabajo: ' . $e->getMessage())
            ->withInput();
    }
}

    public function show(Trabajo $trabajo)
    {
        $trabajo->load(['empleado', 'cliente', 'trabajoServicios.servicio']);
        return view('trabajos.show', compact('trabajo'));
    }

    public function edit(Trabajo $trabajo)
    {
        $servicios = Servicio::all();
        $empleados = Empleado::with('cargo')->get();
        $trabajo->load(['trabajoServicios.servicio']);
        return view('trabajos.edit', compact('trabajo', 'servicios', 'empleados'));
    }



    public function destroy(Trabajo $trabajo)
    {
        $trabajo->delete();
        return redirect()->route('trabajos.index')
            ->with('success', 'Trabajo eliminado exitosamente.');
    }

    public function buscarCliente(Request $request)
    {
        $cliente = Cliente::where('placas', strtoupper($request->placas))->first();
        
        if ($cliente) {
            return response()->json([
                'existe' => true,
                'telefono' => $cliente->telefono,
            ]);
        }
        
        return response()->json(['existe' => false]);
    }

    /**
     * Generar detalle de venta en PDF
     */
    public function detalleVenta(Trabajo $trabajo)
    {
        $trabajo->load(['cliente', 'empleado', 'trabajoServicios.servicio', 'trabajoInventarios.inventario']);
        
        $pdf = Pdf::loadView('trabajos.detalle-venta-pdf', compact('trabajo'));
        
        $nombreArchivo = 'detalle_trabajo_' . $trabajo->id_trabajo . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }

    /**
     * Mostrar trabajos asignados al técnico autenticado
     */
    public function misTrabajosIndex()
    {
        $user = auth()->user();
        
        if (!$user->id_empleado) {
            return redirect()->route('dashboard')->with('error', 'No tienes un empleado asignado.');
        }

        $trabajos = Trabajo::with(['empleado', 'cliente', 'trabajoServicios.servicio'])
            ->where('id_empleado', $user->id_empleado)
            ->orderBy('fecha_trabajo', 'desc')
            ->paginate(30);
        
        return view('trabajos.mis-trabajos', compact('trabajos'));
    }
}