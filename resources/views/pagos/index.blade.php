@extends('adminlte::page')

@section('title', 'Pagos a Técnicos - Chankas Car')

@section('content_header')
    <h1>Pagos a Técnicos</h1>
@stop

@section('content')
    <!-- Alertas de Saldos Pendientes -->
    @if($saldosPendientes->count() > 0)
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Saldos Pendientes</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($saldosPendientes as $item)
                        <div class="col-md-6 col-lg-4">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ $item['empleado']->nombre }} {{ $item['empleado']->apellido }}</span>
                                    <span class="info-box-number">Bs {{ number_format($item['saldo'], 2) }}</span>
                                    <button type="button" class="btn btn-sm btn-light mt-2" 
                                            data-toggle="modal" 
                                            data-target="#modal-pagar-saldo"
                                            data-empleado-id="{{ $item['empleado']->id_empleado }}"
                                            data-empleado-nombre="{{ $item['empleado']->nombre }} {{ $item['empleado']->apellido }}"
                                            data-saldo="{{ $item['saldo'] }}">
                                        <i class="fas fa-money-bill"></i> Pagar Saldo
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filtrar Pagos por Técnico y Fechas</h3>
            <div class="card-tools">
                <a href="{{ route('pagos.index-agrupado', request()->all()) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-layer-group"></i> Vista Agrupada
                </a>
            </div>
        </div>
        <form action="{{ route('pagos.index') }}" method="GET" id="form-filtros">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="id_empleado">Técnico</label>
                            <select class="form-control" id="id_empleado" name="id_empleado" required>
                                <option value="">Seleccione un técnico...</option>
                                @foreach($empleados as $empleado)
                                    <option value="{{ $empleado->id_empleado }}" 
                                        {{ request('id_empleado') == $empleado->id_empleado ? 'selected' : '' }}>
                                        {{ $empleado->nombre }} {{ $empleado->apellido }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="fecha_inicio" 
                                   name="fecha_inicio" 
                                   value="{{ request('fecha_inicio') }}"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_fin">Fecha Fin</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="fecha_fin" 
                                   name="fecha_fin" 
                                   value="{{ request('fecha_fin') }}"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @if($empleadoSeleccionado)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user"></i> 
                    Detalle de Pagos: {{ $empleadoSeleccionado->nombre }} {{ $empleadoSeleccionado->apellido }}
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-registrar-pago">
                        <i class="fas fa-dollar-sign"></i> Registrar Pago
                    </button>
                    <a href="{{ route('pagos.exportar-pdf', ['id_empleado' => request('id_empleado'), 'fecha_inicio' => request('fecha_inicio'), 'fecha_fin' => request('fecha_fin')]) }}" 
                       class="btn btn-danger btn-sm text-white" 
                       target="_blank">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    @if($empleadoSeleccionado)
                        {{-- SALDO ANTERIOR: Deuda/Crédito de períodos pasados --}}
                        @if($saldoAnterior != 0)
                        <div class="col-md-3">
                            <div class="info-box {{ $saldoAnterior > 0 ? 'bg-danger' : 'bg-info' }}">
                                <span class="info-box-icon"><i class="fas fa-history"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ $saldoAnterior > 0 ? '⚠️ Deuda Anterior' : '💰 Adelanto Anterior' }}</span>
                                    <span class="info-box-number">Bs {{ number_format(abs($saldoAnterior), 2) }}</span>
                                    <span class="progress-description">{{ $saldoAnterior > 0 ? 'Se le debe' : 'Pagado de más' }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif
                    
                    {{-- COMISIONES DEL PERÍODO: Trabajo realizado en el rango de fechas --}}
                    <div class="{{ $empleadoSeleccionado && $saldoAnterior != 0 ? 'col-md-3' : 'col-md-4' }}">
                        <div class="info-box bg-primary">
                            <span class="info-box-icon"><i class="fas fa-wrench"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">🔧 Comisiones Trabajadas</span>
                                <span class="info-box-number">Bs {{ number_format($comisionesPeriodo, 2) }}</span>
                                <span class="progress-description">{{ $empleadoSeleccionado ? 'En este período' : 'Total histórico' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- PAGOS DEL PERÍODO: Dinero entregado en el rango de fechas --}}
                    <div class="{{ $empleadoSeleccionado && $saldoAnterior != 0 ? 'col-md-3' : 'col-md-4' }}">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-hand-holding-usd"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">💵 Dinero Entregado</span>
                                <span class="info-box-number">Bs {{ number_format($pagosPeriodo, 2) }}</span>
                                <span class="progress-description">{{ $empleadoSeleccionado ? 'En este período' : 'Total histórico' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- SALDO FINAL: Saldo Anterior + Comisiones - Pagos --}}
                    <div class="{{ $empleadoSeleccionado && $saldoAnterior != 0 ? 'col-md-3' : 'col-md-4' }}">
                        <div class="info-box {{ $saldoFinal > 0 ? 'bg-warning' : ($saldoFinal < 0 ? 'bg-info' : 'bg-secondary') }}">
                            <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ $saldoFinal > 0 ? '⚠️ SALDO PENDIENTE' : ($saldoFinal < 0 ? '💰 ADELANTO' : '✅ SALDADO') }}</span>
                                <span class="info-box-number">Bs {{ number_format(abs($saldoFinal), 2) }}</span>
                                <span class="progress-description">
                                    @if($empleadoSeleccionado && $saldoAnterior != 0)
                                        {{ $saldoAnterior > 0 ? number_format($saldoAnterior, 2) : '0.00' }} + {{ number_format($comisionesPeriodo, 2) }} - {{ number_format($pagosPeriodo, 2) }}
                                    @else
                                        {{ $saldoFinal > 0 ? 'Por pagar' : ($saldoFinal < 0 ? 'Pagado de más' : 'Cuadrado') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($trabajos->count() > 0)
                    @php
                        $trabajosPorFecha = $trabajos->groupBy(function($trabajo) {
                            return $trabajo->fecha_trabajo->format('Y-m-d');
                        });
                    @endphp

                    @foreach($trabajosPorFecha as $fecha => $trabajosDia)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar-day"></i> 
                                    {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                                    <span class="badge badge-primary float-right">
                                        {{ $trabajosDia->count() }} trabajo(s) - Bs {{ number_format($trabajosDia->sum('total_tecnico'), 2) }}
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Placa</th>
                                            <th>Servicios Realizados</th>
                                            <th class="text-right">Comisión</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($trabajosDia as $trabajo)
                                            <tr>
                                                <td><strong>{{ $trabajo->cliente ? $trabajo->cliente->placas : 'SIN PLACA' }}</strong></td>
                                                <td>
                                                    <ul class="mb-0 pl-3">
                                                        @foreach($trabajo->trabajoServicios as $ts)
                                                            <li>
                                                                <small>
                                                                    {{ $ts->servicio->nombre }}
                                                                    @if($ts->cantidad > 1)
                                                                        <span class="badge badge-info">x{{ $ts->cantidad }}</span>
                                                                    @endif
                                                                    (Bs {{ number_format($ts->importe_tecnico, 2) }})
                                                                </small>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td class="text-right">Bs {{ number_format($trabajo->total_tecnico, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="2" class="text-right"><strong>Subtotal del día:</strong></td>
                                            <td class="text-right">
                                                <strong>Bs {{ number_format($trabajosDia->sum('total_tecnico'), 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <div class="card {{ $saldoPendiente > 0 ? 'bg-warning' : 'bg-success' }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="mb-0">
                                        @if($saldoPendiente > 0)
                                            PAGO PENDIENTE
                                        @else
                                            PAGADO COMPLETAMENTE
                                        @endif
                                    </h4>
                                    <small>Del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</small>
                                </div>
                                <div class="col-md-6 text-right">
                                    @if($saldoPendiente > 0)
                                        <h3 class="mb-0">Bs {{ number_format($saldoPendiente, 2) }}</h3>
                                        <small>Saldo pendiente de pago</small>
                                    @else
                                        <h3 class="mb-0"><i class="fas fa-check-circle"></i> Saldo: Bs 0.00</h3>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        No se encontraron trabajos para este técnico en el rango de fechas seleccionado.
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center text-muted">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <p>Seleccione un técnico y un rango de fechas para ver el detalle de pagos.</p>
            </div>
        </div>
    @endif

    <!-- Modal Registrar Pago -->
    <div class="modal fade" id="modal-registrar-pago" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('pagos.registrar') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success">
                        <h5 class="modal-title">Registrar Pago</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_empleado" value="{{ request('id_empleado') }}">
                        <input type="hidden" name="periodo_inicio" value="{{ request('fecha_inicio') }}">
                        <input type="hidden" name="periodo_fin" value="{{ request('fecha_fin') }}">

                        <div class="form-group">
                            <label>Técnico</label>
                            <input type="text" class="form-control" value="{{ $empleadoSeleccionado ? $empleadoSeleccionado->nombre . ' ' . $empleadoSeleccionado->apellido : '' }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Total a Pagar</label>
                            <input type="text" class="form-control" value="Bs {{ number_format($totalComision ?? 0, 2) }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="fecha_pago">Fecha de Pago</label>
                            <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="monto_pagado">Monto a Pagar</label>
                            <input type="number" class="form-control" id="monto_pagado" name="monto_pagado" step="0.01" min="0.01" value="{{ $totalComision ?? 0 }}" required>
                        </div>

                        <div class="form-group">
                            <label for="tipo_pago">Tipo de Pago</label>
                            <select class="form-control" id="tipo_pago" name="tipo_pago" required>
                                <option value="completo">Pago Completo</option>
                                <option value="parcial">Pago Parcial</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="observaciones_pago">Observaciones</label>
                            <textarea class="form-control" id="observaciones_pago" name="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Registrar Pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Pagar Saldo -->
    <div class="modal fade" id="modal-pagar-saldo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('pagos.pagar-saldo') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Pagar Saldo Pendiente</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_empleado" id="saldo-empleado-id">

                        <div class="form-group">
                            <label>Técnico</label>
                            <input type="text" class="form-control" id="saldo-empleado-nombre" readonly>
                        </div>

                        <div class="form-group">
                            <label>Saldo Pendiente</label>
                            <input type="text" class="form-control" id="saldo-pendiente-texto" readonly>
                        </div>

                        <div class="form-group">
                            <label for="monto_pagar_saldo">Monto a Pagar</label>
                            <input type="number" class="form-control" id="monto_pagar_saldo" name="monto_pagado" step="0.01" min="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="observaciones_saldo">Observaciones</label>
                            <textarea class="form-control" id="observaciones_saldo" name="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Pagar Saldo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Historial de Pagos Realizados -->
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history"></i> Historial de Pagos Realizados</h3>
        </div>
        <div class="card-body">
            <!-- Estadísticas rápidas -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Pagos Realizados (Histórico)</span>
                            <span class="info-box-number">Bs {{ number_format($totalPagosRealizados, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pagos del Mes Actual</span>
                            <span class="info-box-number">Bs {{ number_format($pagosMesActual, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($historialPagos->count() > 0)
                <div class="table-responsive">
                    <table id="tabla-historial" class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha Pago</th>
                                <th>Técnico</th>
                                <th>Período</th>
                                <th>Tipo</th>
                                <th class="text-right">Monto Pagado</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historialPagos as $pago)
                                <tr>
                                    <td>{{ $pago->id_pago }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                                    <td>
                                        <strong>{{ $pago->empleado->nombre }} {{ $pago->empleado->apellido }}</strong>
                                        <br><small class="text-muted">{{ $pago->empleado->cargo->nombre }}</small>
                                    </td>
                                    <td>
                                        <small>
                                            {{ \Carbon\Carbon::parse($pago->periodo_inicio)->format('d/m/Y') }} - 
                                            {{ \Carbon\Carbon::parse($pago->periodo_fin)->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($pago->tipo_pago == 'completo')
                                            <span class="badge badge-success">Completo</span>
                                        @elseif($pago->tipo_pago == 'parcial')
                                            <span class="badge badge-warning">Parcial</span>
                                        @else
                                            <span class="badge badge-info">Saldo</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <strong class="text-success">Bs {{ number_format($pago->monto_pagado, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($pago->observaciones)
                                            <small>{{ Str::limit($pago->observaciones, 40) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <td colspan="5" class="text-right"><strong>Total en página:</strong></td>
                                <td class="text-right">
                                    <strong>Bs {{ number_format($historialPagos->sum('monto_pagado'), 2) }}</strong>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <!-- Paginación -->
                    <div class="mt-3">
                        {{ $historialPagos->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay pagos registrados aún.
                </div>
            @endif
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    @vite('resources/css/adminlte-theme.css')
@stop

@section('js')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        // Inicializar DataTable directamente
        $('#tabla-historial').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
            },
            "order": [[0, "desc"]],
            "pageLength": 25,
            "responsive": true
        });
    });

    // Cargar datos al modal de pagar saldo
    $('#modal-pagar-saldo').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var empleadoId = button.data('empleado-id');
        var empleadoNombre = button.data('empleado-nombre');
        var saldo = button.data('saldo');
        
        var modal = $(this);
        modal.find('#saldo-empleado-id').val(empleadoId);
        modal.find('#saldo-empleado-nombre').val(empleadoNombre);
        modal.find('#saldo-pendiente-texto').val('Bs ' + parseFloat(saldo).toFixed(2));
        modal.find('#monto_pagar_saldo').val(parseFloat(saldo).toFixed(2));
        modal.find('#monto_pagar_saldo').attr('max', parseFloat(saldo).toFixed(2));
    });
</script>
@stop