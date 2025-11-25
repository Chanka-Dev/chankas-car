@extends('layouts.base')

@section('title', 'Proveedores - Chankas Car')

@section('content_header')
    <h1>Gestión de Proveedores</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Proveedores</h3>
            <div class="card-tools">
                @isAdmin
                    <a href="{{ route('proveedores.create') }}" class="btn btn-success btn-sm text-white">
                        <i class="fas fa-plus"></i> Nuevo Proveedor
                    </a>
                @endisAdmin
            </div>
        </div>
        <div class="card-body">
            <table id="proveedores-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Dirección</th>
                        <th>Items en Inventario</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proveedores as $proveedor)
                        <tr>
                            <td>{{ $proveedor->id_proveedor }}</td>
                            <td><strong>{{ $proveedor->nombre }}</strong></td>
                            <td>{{ $proveedor->telefono ?? 'N/A' }}</td>
                            <td>{{ $proveedor->email ?? 'N/A' }}</td>
                            <td>{{ $proveedor->direccion ? Str::limit($proveedor->direccion, 30) : 'N/A' }}</td>
                            <td>
                                <span class="badge badge-info">{{ $proveedor->inventarios_count }}</span>
                            </td>
                            <td>{{ $proveedor->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('proveedores.edit', $proveedor->id_proveedor) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form id="delete-form-{{ $proveedor->id_proveedor }}" action="{{ route('proveedores.destroy', $proveedor->id_proveedor) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion('delete-form-{{ $proveedor->id_proveedor }}', 'el proveedor {{ $proveedor->nombre }}')">
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
            $('#proveedores-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                }
            });
        });
    </script>
@endpush