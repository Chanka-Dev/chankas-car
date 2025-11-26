@extends('layouts.base')

@section('title', 'Clientes - Chankas Car')

@section('content_header')
    <h1>Gestión de Clientes</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Clientes</h3>
            <div class="card-tools">
                @canManage
                    <a href="{{ route('clientes.create') }}" class="btn btn-success btn-sm text-white">
                        <i class="fas fa-plus"></i> Nuevo Cliente
                    </a>
                @endcanManage
            </div>
        </div>
        
        <!-- Búsqueda -->
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('clientes.index') }}" class="form-inline">
                <div class="input-group input-group-sm mr-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" name="buscar" class="form-control" 
                           placeholder="Buscar por placa o teléfono..." 
                           value="{{ request('buscar') }}">
                </div>
                <button type="submit" class="btn btn-primary btn-sm mr-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                @if(request('buscar'))
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                @endif
                <span class="ml-auto badge badge-info">Total: {{ $clientes->total() }} clientes</span>
            </form>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Placas</th>
                        <th>Teléfono</th>
                        <th>Total Trabajos</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->id_cliente }}</td>
                            <td><strong>{{ $cliente->placas }}</strong></td>
                            <td>{{ $cliente->telefono ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($cliente->trabajos_count > 0)
                                    <span class="badge badge-{{ $cliente->trabajos_count > 3 ? 'success' : 'primary' }}">{{ $cliente->trabajos_count }}</span>
                                @else
                                    <span class="badge badge-secondary">0</span>
                                @endif
                            </td>
                            <td>{{ $cliente->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('clientes.edit', $cliente->id_cliente) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form id="delete-form-{{ $cliente->id_cliente }}" action="{{ route('clientes.destroy', $cliente->id_cliente) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion('delete-form-{{ $cliente->id_cliente }}', 'el cliente con placas {{ $cliente->placas }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
                    Mostrando {{ $clientes->firstItem() ?? 0 }} - {{ $clientes->lastItem() ?? 0 }} de {{ $clientes->total() }} clientes
                </small>
            </div>
            <div class="float-right">
                {{ $clientes->onEachSide(1)->links('pagination::bootstrap-4') }}
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
