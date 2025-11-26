@extends('layouts.base')

@section('title', 'Trabajos - Chankas Car')

@section('content_header')
    <h1>Trabajos Realizados</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Trabajos</h3>
            <div class="card-tools">
                @canEdit
                    <a href="{{ route('trabajos.create') }}" class="btn btn-success btn-sm text-white">
                        <i class="fas fa-plus"></i> Nuevo Trabajo
                    </a>
                @endcanEdit
            </div>
        </div>
        
        <!-- Filtros de búsqueda -->
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('trabajos.index') }}">
                <div class="row">
                    <!-- Búsqueda general -->
                    <div class="col-md-4 mb-2">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" name="buscar" class="form-control" 
                                   placeholder="Buscar por placa, técnico, servicio, observación..." 
                                   value="{{ request('buscar') }}">
                        </div>
                    </div>
                    
                    <!-- Filtro por técnico -->
                    <div class="col-md-3 mb-2">
                        <select name="id_empleado" class="form-control form-control-sm">
                            <option value="">Todos los técnicos</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id_empleado }}" 
                                    {{ request('id_empleado') == $empleado->id_empleado ? 'selected' : '' }}>
                                    {{ $empleado->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Fecha desde -->
                    <div class="col-md-2 mb-2">
                        <input type="date" name="fecha_desde" class="form-control form-control-sm" 
                               placeholder="Desde" value="{{ request('fecha_desde') }}">
                    </div>
                    
                    <!-- Fecha hasta -->
                    <div class="col-md-2 mb-2">
                        <input type="date" name="fecha_hasta" class="form-control form-control-sm" 
                               placeholder="Hasta" value="{{ request('fecha_hasta') }}">
                    </div>
                    
                    <!-- Botones -->
                    <div class="col-md-1 mb-2">
                        <button type="submit" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Segunda fila con info y botón limpiar -->
                <div class="row">
                    <div class="col-md-6">
                        @if(request()->hasAny(['buscar', 'id_empleado', 'fecha_desde', 'fecha_hasta']))
                            <a href="{{ route('trabajos.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-times"></i> Limpiar filtros
                            </a>
                            <span class="ml-2 text-muted small">
                                <i class="fas fa-filter"></i> Filtros activos
                            </span>
                        @endif
                    </div>
                    <div class="col-md-6 text-right">
                        <span class="badge badge-info">Total: {{ $trabajos->total() }} trabajos</span>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">N°</th>
                            <th style="width: 100px;">Fecha</th>
                            <th style="width: 100px;">Fecha Rec.</th>
                            <th>Técnico</th>
                            <th style="width: 100px;">Placa</th>
                            <th>Trabajos Realizados</th>
                            <th style="width: 100px;" class="text-right">Total Cliente</th>
                            <th style="width: 100px;" class="text-right">Total Téc.</th>
                            <th>Observaciones</th>
                            <th style="width: 100px;">Celular</th>
                            <th style="width: 150px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trabajos as $trabajo)
                            <tr>
                                <td>{{ $trabajo->id_trabajo }}</td>
                                <td>{{ $trabajo->fecha_trabajo->format('d/m/Y') }}</td>
                                <td>
                                    {{ $trabajo->fecha_recepcion->format('d/m/Y') }}
                                    @if($trabajo->fecha_recalificacion)
                                        <br><small class="text-muted">Recal: {{ $trabajo->fecha_recalificacion->format('d/m/Y') }}</small>
                                    @endif
                                </td>
                                <td>{{ $trabajo->empleado->nombre }}</td>
                                <td><strong>{{ $trabajo->cliente ? $trabajo->cliente->placas : 'SIN PLACA' }}</strong></td>
                                <td>
                                    @if($trabajo->trabajoServicios->count() > 0)
                                        <ul class="mb-0 pl-3">
                                            @foreach($trabajo->trabajoServicios as $ts)
                                                <li>
                                                    <small>
                                                        {{ $ts->servicio->nombre }}
                                                        @if($ts->cantidad > 1)
                                                            <span class="badge badge-info">x{{ $ts->cantidad }}</span>
                                                        @endif
                                                    </small>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">Sin servicios</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <strong class="text-success">{{ number_format($trabajo->total_cliente, 2) }}</strong>
                                </td>
                                <td class="text-right">
                                    <strong class="text-primary">{{ number_format($trabajo->total_tecnico, 2) }}</strong>
                                </td>
                                <td>
                                    @if($trabajo->observaciones)
                                        <small>{{ Str::limit($trabajo->observaciones, 30) }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $trabajo->cliente && $trabajo->cliente->telefono ? $trabajo->cliente->telefono : 'N/A' }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('trabajos.detalle-venta', $trabajo->id_trabajo) }}" class="btn btn-warning btn-sm" title="Generar PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        @canEdit
                                            <a href="{{ route('trabajos.edit', $trabajo->id_trabajo) }}" class="btn btn-primary btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="delete-form-{{ $trabajo->id_trabajo }}" action="{{ route('trabajos.destroy', $trabajo->id_trabajo) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion('delete-form-{{ $trabajo->id_trabajo }}', 'el trabajo #{{ $trabajo->id_trabajo }}')" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcanEdit
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Paginación -->
        <div class="card-footer clearfix">
            <div class="float-left">
                <small class="text-muted">
                    Mostrando {{ $trabajos->firstItem() ?? 0 }} - {{ $trabajos->lastItem() ?? 0 }} de {{ $trabajos->total() }} trabajos
                </small>
            </div>
            <div class="float-right">
                {{ $trabajos->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        function confirmarEliminacion(formId, nombre) {
            if (confirm('¿Estás seguro de eliminar ' + nombre + '?')) {
                document.getElementById(formId).submit();
            }
        }
    </script>
@endpush