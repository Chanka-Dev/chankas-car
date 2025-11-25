@extends('layouts.base')

@section('title', 'Empleados - Chankas Car')

@section('content_header')
    <h1>Gestión de Empleados</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Empleados</h3>
            <div class="card-tools">
                @isAdmin
                    <a href="{{ route('empleados.create') }}" class="btn btn-success btn-sm text-white">
                        <i class="fas fa-plus"></i> Nuevo Empleado
                    </a>
                @endisAdmin
            </div>
        </div>
        <div class="card-body">
            <table id="empleados-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>CI</th>
                        <th>Nombre Completo</th>
                        <th>Teléfono</th>
                        <th>Cargo</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empleados as $empleado)
                        <tr>
                            <td>{{ $empleado->id_empleado }}</td>
                            <td>{{ $empleado->ci }}</td>
                            <td>{{ $empleado->nombre }} {{ $empleado->apellido }}</td>
                            <td>{{ $empleado->telefono ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-info">{{ $empleado->cargo->nombre }}</span>
                            </td>
                            <td>{{ $empleado->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('empleados.edit', $empleado->id_empleado) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form id="delete-form-{{ $empleado->id_empleado }}" action="{{ route('empleados.destroy', $empleado->id_empleado) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion('delete-form-{{ $empleado->id_empleado }}', '{{ $empleado->nombre }} {{ $empleado->apellido }}')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#empleados-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                }
            });
        });
    </script>
@endpush