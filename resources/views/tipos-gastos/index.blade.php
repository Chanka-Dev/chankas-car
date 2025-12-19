@extends('layouts.base')

@section('title', 'Tipos de Gastos - Chankas Car')

@section('content_header')
    <h1>Gestión de Tipos de Gastos</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Listado de Tipos de Gastos
            </h3>
            <div class="card-tools">
                <a href="{{ route('tipos-gastos.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Tipo de Gasto
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card-body pb-0">
            <form action="{{ route('tipos-gastos.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <input type="text" 
                                   class="form-control" 
                                   name="buscar" 
                                   placeholder="Buscar por nombre..." 
                                   value="{{ request('buscar') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control" name="estado">
                                <option value="">Todos los estados</option>
                                <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                                <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('tipos-gastos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            @if($tiposGastos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="30%">Nombre</th>
                                <th width="35%">Descripción</th>
                                <th width="10%" class="text-center">Estado</th>
                                <th width="10%" class="text-center">Registros</th>
                                <th width="10%" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tiposGastos as $tipo)
                                <tr>
                                    <td>{{ $tipo->id_tipo_gasto }}</td>
                                    <td>
                                        <strong>{{ $tipo->nombre }}</strong>
                                    </td>
                                    <td>
                                        {{ Str::limit($tipo->descripcion, 60) ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if($tipo->activo)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Activo
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-ban"></i> Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $count = \App\Models\GastoTaller::where('concepto', $tipo->nombre)->count();
                                        @endphp
                                        @if($count > 0)
                                            <span class="badge badge-info">{{ $count }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('tipos-gastos.show', $tipo->id_tipo_gasto) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tipos-gastos.edit', $tipo->id_tipo_gasto) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(!$tipo->tieneGastos())
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        onclick="confirmarEliminacion({{ $tipo->id_tipo_gasto }})"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <button type="button" 
                                                        class="btn btn-sm btn-secondary" 
                                                        title="No se puede eliminar (tiene registros)"
                                                        disabled>
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            @endif
                                        </div>

                                        <form id="delete-form-{{ $tipo->id_tipo_gasto }}" 
                                              action="{{ route('tipos-gastos.destroy', $tipo->id_tipo_gasto) }}" 
                                              method="POST" 
                                              style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-3">
                    {{ $tiposGastos->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No se encontraron tipos de gastos. 
                    <a href="{{ route('tipos-gastos.create') }}">Crear el primero</a>
                </div>
            @endif
        </div>
    </div>
@stop

@section('css')
    <style>
        .table td {
            vertical-align: middle;
        }
    </style>
@stop

@push('scripts')
    <script>
        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        // Auto-cerrar alertas después de 5 segundos
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
@endpush
