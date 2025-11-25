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
        <div class="card-body">
            <div class="table-responsive">
                <table id="trabajos-table" class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Fecha</th>
                            <th>Fecha Rec.</th>
                            <th>Técnico</th>
                            <th>Placa</th>
                            <th>Trabajos Realizados</th>
                            <th>Total Cliente (Bs)</th>
                            <th>Total Téc. (Bs)</th>
                            <th>Observaciones</th>
                            <th>Celular</th>
                            <th>Acciones</th>
                            <th>Visitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trabajos as $index => $trabajo)
                            <tr>
                                <td>{{ count($trabajos) - $index }}</td>
                                <td>{{ $trabajo->fecha_trabajo->format('d/m/Y') }}</td>
                                <td>
                                    {{ $trabajo->fecha_recepcion->format('d/m/Y') }}
                                    @if($trabajo->fecha_recalificacion)
                                        <br><small class="text-muted">Recal: {{ $trabajo->fecha_recalificacion->format('d/m/Y') }}</small>
                                    @endif
                                </td>
                                <td>{{ $trabajo->empleado->nombre }} {{ $trabajo->empleado->apellido }}</td>
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
                                <td>
                                    <a href="{{ route('trabajos.detalle-venta', $trabajo->id_trabajo) }}" class="btn btn-warning btn-xs" title="Generar PDF" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    @canEdit
                                        <a href="{{ route('trabajos.edit', $trabajo->id_trabajo) }}" class="btn btn-primary btn-xs" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form id="delete-form-{{ $trabajo->id_trabajo }}" action="{{ route('trabajos.destroy', $trabajo->id_trabajo) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-xs" onclick="confirmarEliminacion('delete-form-{{ $trabajo->id_trabajo }}', 'el trabajo #{{ $trabajo->id_trabajo }}')" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcanEdit
                                </td>
                                <td class="text-center">
                                    @if($trabajo->id_cliente && isset($trabajosPorPlaca[$trabajo->id_cliente]))
                                        @php
                                            $visitas = $trabajosPorPlaca[$trabajo->id_cliente];
                                        @endphp
                                        @if($visitas > 1)
                                            <span class="badge badge-warning" title="{{ $visitas }} trabajos realizados">
                                                <i class="fas fa-redo"></i> {{ $visitas }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-check"></i> {{ $visitas }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
                "order": [[1, "desc"]],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ],
                "pageLength": 25
            });
        });
    </script>
@endpush