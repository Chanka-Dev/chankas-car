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
        <div class="card-body">
            <table id="clientes-table" class="table table-bordered table-striped">
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
                            <td>
                                <span class="badge badge-primary">{{ $cliente->trabajos->count() }}</span>
                            </td>
                            <td>{{ $cliente->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('clientes.edit', $cliente->id_cliente) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form id="delete-form-{{ $cliente->id_cliente }}" action="{{ route('clientes.destroy', $cliente->id_cliente) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion('delete-form-{{ $cliente->id_cliente }}', 'el cliente con placas {{ $cliente->placas }}')">
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
            $('#clientes-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                }
            });
        });
    </script>
@endpush
