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
        <div class="card-body">
            <table id="servicios-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Costo Base (Bs)</th>
                        <th>Comisión Base (Bs)</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($servicios as $servicio)
                        <tr>
                            <td>{{ $servicio->id_servicio }}</td>
                            <td>{{ $servicio->nombre }}</td>
                            <td>{{ number_format($servicio->costo, 2) }}</td>
                            <td>{{ number_format($servicio->comision, 2) }}</td>
                            <td>{{ $servicio->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('servicios.edit', $servicio->id_servicio) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                
                                @if($servicio->trabajo_servicios_count > 0)
                                    <button type="button" class="btn btn-secondary btn-sm" disabled title="No se puede eliminar: tiene {{ $servicio->trabajo_servicios_count }} trabajo(s) asociado(s)">
                                        <i class="fas fa-lock"></i> Protegido
                                    </button>
                                @else
                                    <form id="delete-form-{{ $servicio->id_servicio }}" action="{{ route('servicios.destroy', $servicio->id_servicio) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion('delete-form-{{ $servicio->id_servicio }}', 'el servicio {{ $servicio->nombre }}')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                @endif
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
            $('#servicios-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                }
            });
        });

        function confirmarEliminacion(formId, nombre) {
            if (confirm('¿Estás seguro de eliminar ' + nombre + '?')) {
                document.getElementById(formId).submit();
            }
        }
    </script>
@endpush