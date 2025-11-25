@extends('layouts.base')

@section('title', 'Mis Trabajos - Chankas Car')

@section('content_header')
    <h1>Mis Trabajos Asignados</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Trabajos</h3>
        </div>
        <div class="card-body">
            @if($trabajos->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No tienes trabajos asignados actualmente.
                </div>
            @else
                <table id="trabajos-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha Trabajo</th>
                            <th>Cliente/Placas</th>
                            <th>Servicios</th>
                            <th>Total Cliente</th>
                            <th>Mi Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trabajos as $trabajo)
                            <tr>
                                <td>{{ $trabajo->id_trabajo }}</td>
                                <td>{{ \Carbon\Carbon::parse($trabajo->fecha_trabajo)->format('d/m/Y') }}</td>
                                <td>
                                    @if($trabajo->cliente)
                                        <strong>{{ $trabajo->cliente->placas }}</strong><br>
                                        <small class="text-muted">{{ $trabajo->cliente->telefono }}</small>
                                    @else
                                        <span class="text-muted">Sin cliente</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach($trabajo->trabajoServicios as $ts)
                                        <span class="badge badge-info">
                                            {{ $ts->servicio->nombre_servicio }} ({{ $ts->cantidad }})
                                        </span>
                                    @endforeach
                                </td>
                                <td class="text-right">
                                    <strong>Bs. {{ number_format($trabajo->trabajoServicios->sum('importe_cliente'), 2) }}</strong>
                                </td>
                                <td class="text-right">
                                    <strong class="text-success">Bs. {{ number_format($trabajo->trabajoServicios->sum('importe_tecnico'), 2) }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('trabajos.show', $trabajo->id_trabajo) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('trabajos.detalle-venta', $trabajo->id_trabajo) }}" class="btn btn-danger btn-sm" title="Descargar PDF">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <th colspan="4" class="text-right">TOTALES:</th>
                            <th class="text-right">
                                Bs. {{ number_format($trabajos->sum(function($t) { 
                                    return $t->trabajoServicios->sum('importe_cliente'); 
                                }), 2) }}
                            </th>
                            <th class="text-right text-success">
                                Bs. {{ number_format($trabajos->sum(function($t) { 
                                    return $t->trabajoServicios->sum('importe_tecnico'); 
                                }), 2) }}
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </div>
@stop

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#trabajos-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                },
                "order": [[1, 'desc']] // Ordenar por fecha descendente
            });
        });
    </script>
@endpush
