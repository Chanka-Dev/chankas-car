@extends('layouts.base')

@section('title', 'Servicios - Chankas Car')

@section('content_header')
    <h1>Gestión de Servicios</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Servicios</h3>
            <div class="card-tools">
                @isAdmin
                    <a href="{{ route('servicios.create') }}" class="btn btn-success btn-sm text-white">
                        <i class="fas fa-plus"></i> Nuevo Servicio
                    </a>
                @endisAdmin
            </div>
        </div>
        
        <!-- Búsqueda -->
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('servicios.index') }}" class="form-inline">
                <div class="input-group input-group-sm mr-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" name="buscar" class="form-control" 
                           placeholder="Buscar por nombre..." 
                           value="{{ request('buscar') }}">
                </div>
                <button type="submit" class="btn btn-primary btn-sm mr-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                @if(request('buscar'))
                    <a href="{{ route('servicios.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                @endif
                <span class="ml-auto badge badge-info">Total: {{ $servicios->total() }} servicio(s)</span>
            </form>
        </div>
        
        <div class="card-body p-0">
            @if($servicios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th class="text-right">Costo (Bs)</th>
                                <th class="text-right">Comisión (Bs)</th>
                                <th class="text-center">Usos</th>
                                <th>Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($servicios as $servicio)
                                <tr>
                                    <td>{{ $servicio->id_servicio }}</td>
                                    <td><strong>{{ $servicio->nombre }}</strong></td>
                                    <td class="text-right">{{ number_format($servicio->costo, 2) }}</td>
                                    <td class="text-right">{{ number_format($servicio->comision, 2) }}</td>
                                    <td class="text-center">
                                        @if($servicio->trabajo_servicios_count > 0)
                                            <span class="badge badge-info">{{ $servicio->trabajo_servicios_count }}</span>
                                        @else
                                            <span class="badge badge-secondary">0</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $servicio->created_at->format('d/m/Y') }}</small></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('servicios.edit', $servicio->id_servicio) }}" class="btn btn-primary btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($servicio->trabajo_servicios_count > 0)
                                                <button type="button" class="btn btn-secondary btn-sm" disabled title="No se puede eliminar: {{ $servicio->trabajo_servicios_count }} uso(s)">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            @else
                                                <form id="delete-form-{{ $servicio->id_servicio }}" action="{{ route('servicios.destroy', $servicio->id_servicio) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion('delete-form-{{ $servicio->id_servicio }}', '{{ $servicio->nombre }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info m-0">
                    <i class="fas fa-info-circle"></i> No se encontraron servicios.
                </div>
            @endif
        </div>

        <!-- Paginación -->
        @if($servicios->hasPages())
            <div class="card-footer clearfix">
                <div class="float-left">
                    <small class="text-muted">
                        Mostrando {{ $servicios->firstItem() ?? 0 }} - {{ $servicios->lastItem() ?? 0 }} de {{ $servicios->total() }} servicios
                    </small>
                </div>
                <div class="float-right">
                    {{ $servicios->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
@stop

@push('scripts')
    <script>
        function confirmarEliminacion(formId, nombre) {
            if (confirm('¿Estás seguro de eliminar el servicio "' + nombre + '"?')) {
                document.getElementById(formId).submit();
            }
        }
    </script>
@endpush