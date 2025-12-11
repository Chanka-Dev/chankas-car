@extends('adminlte::page')

@section('title', 'Gastos del Taller - Chankas Car')

@section('content_header')
    <h1>Gastos del Taller</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filtros de Búsqueda</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('gastos.index') }}" method="GET" class="form-inline">
                <div class="form-group mr-2 mb-2">
                    <label for="fecha_desde" class="mr-2">Desde:</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>
                <div class="form-group mr-2 mb-2">
                    <label for="fecha_hasta" class="mr-2">Hasta:</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="form-group mr-2 mb-2">
                    <label for="concepto" class="mr-2">Concepto:</label>
                    <input type="text" name="concepto" id="concepto" class="form-control" placeholder="Buscar..." value="{{ request('concepto') }}">
                </div>
                <button type="submit" class="btn btn-primary mr-2 mb-2">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="{{ route('gastos.index') }}" class="btn btn-secondary mb-2">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Gastos</h3>
            <div class="card-tools">
                @canManage
                    <a href="{{ route('gastos.create') }}" class="btn btn-success btn-sm text-white">
                        <i class="fas fa-plus"></i> Nuevo Gasto
                    </a>
                @endcanManage
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Resumen de gastos del mes -->
            @php
                // Para el resumen del mes, necesitamos todos los gastos, no solo la página actual
                // Vamos a usar solo los de la página actual para el resumen
                $gastosMesActual = collect($gastos->items())->filter(function($gasto) {
                    $fecha = is_string($gasto['fecha']) ? \Carbon\Carbon::parse($gasto['fecha']) : $gasto['fecha'];
                    return $fecha->month == date('m') && $fecha->year == date('Y');
                });
                $totalMes = $gastosMesActual->sum('monto');
            @endphp

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="info-box bg-gradient-danger">
                        <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total de Gastos Este Mes</span>
                            <span class="info-box-number">Bs {{ number_format($totalMes, 2) }}</span>
                            <span class="info-box-text">{{ $gastosMesActual->count() }} gasto(s) registrado(s) en {{ date('F Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Concepto</th>
                            <th>Descripción</th>
                            <th>Monto (Bs)</th>
                            <th>Comprobante</th>
                            <th>Empleado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gastos as $gasto)
                            <tr>
                                <td>
                                    {{ is_string($gasto['fecha']) ? \Carbon\Carbon::parse($gasto['fecha'])->format('d/m/Y') : $gasto['fecha']->format('d/m/Y') }}
                                </td>
                                <td>
                                    @if($gasto['tipo'] === 'pago_tecnico')
                                        <span class="badge badge-info">Pago Técnico</span>
                                    @else
                                        <span class="badge badge-warning">Gasto Taller</span>
                                    @endif
                                </td>
                                <td><strong>{{ $gasto['concepto'] }}</strong></td>
                                <td>{{ $gasto['descripcion'] ? Str::limit($gasto['descripcion'], 50) : '-' }}</td>
                                <td class="text-right">
                                    <span class="badge badge-danger">Bs {{ number_format($gasto['monto'], 2) }}</span>
                                </td>
                                <td>{{ $gasto['comprobante'] ?? 'N/A' }}</td>
                                <td>{{ $gasto['empleado'] ? $gasto['empleado']->nombre . ' ' . $gasto['empleado']->apellido : 'Sistema' }}</td>
                                <td>
                                    @if($gasto['tipo'] === 'gasto_taller')
                                        @canManage
                                            <a href="{{ route('gastos.edit', $gasto['id']) }}" class="btn btn-primary btn-xs">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('gastos.destroy', $gasto['id']) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('¿Está seguro de eliminar este gasto?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcanManage
                                    @else
                                        <a href="{{ route('pagos.index') }}" class="btn btn-info btn-xs" title="Ver en Pagos">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No se encontraron registros</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                {{-- Paginación --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Mostrando {{ $gastos->firstItem() ?? 0 }} a {{ $gastos->lastItem() ?? 0 }} de {{ $gastos->total() }} registros
                    </div>
                    <div>
                        {{ $gastos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen por concepto -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Gastos por Concepto (Este Mes)</h3>
                </div>
                <div class="card-body">
                    @php
                        $gastosPorConcepto = $gastosMesActual->groupBy('concepto')->map(function($items) {
                            return [
                                'total' => $items->sum('monto'),
                                'cantidad' => $items->count()
                            ];
                        })->sortByDesc('total');
                    @endphp

                    @if($gastosPorConcepto->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gastosPorConcepto as $concepto => $data)
                                    <tr>
                                        <td>{{ Str::limit($concepto, 30) }}</td>
                                        <td class="text-center">{{ $data['cantidad'] }}</td>
                                        <td class="text-right">Bs {{ number_format($data['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No hay gastos registrados este mes.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Últimos 5 Gastos</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach(collect($gastos->items())->take(5) as $gasto)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ Str::limit($gasto['concepto'], 40) }}</strong><br>
                                    <small class="text-muted">
                                        {{ is_string($gasto['fecha']) ? \Carbon\Carbon::parse($gasto['fecha'])->format('d/m/Y') : $gasto['fecha']->format('d/m/Y') }}
                                        @if($gasto['tipo'] === 'pago_tecnico')
                                            <span class="badge badge-info badge-sm">Pago</span>
                                        @else
                                            <span class="badge badge-warning badge-sm">Gasto</span>
                                        @endif
                                    </small>
                                </div>
                                <span class="badge badge-danger badge-pill">Bs {{ number_format($gasto['monto'], 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    @vite('resources/css/adminlte-theme.css')
@stop

@section('js')
    {{-- Sin necesidad de DataTables, usamos paginación Laravel --}}
@stop
