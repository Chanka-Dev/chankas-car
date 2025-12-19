@extends('adminlte::page')

@section('title', 'Dashboard - Chankas Car')

@section('content_header')
    <h1>Panel de Control</h1>
@stop

@section('content')
    <!-- Filtro de Fechas -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-2">
                    <form action="{{ route('dashboard') }}" method="GET" class="form-inline">
                        <label class="mr-2"><i class="fas fa-filter"></i> Filtrar por periodo:</label>
                        <div class="input-group input-group-sm mr-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Desde</span>
                            </div>
                            <input type="date" name="fecha_desde" class="form-control" value="{{ $fechaDesde }}" required>
                        </div>
                        <div class="input-group input-group-sm mr-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Hasta</span>
                            </div>
                            <input type="date" name="fecha_hasta" class="form-control" value="{{ $fechaHasta }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm mr-2">
                            <i class="fas fa-search"></i> Aplicar
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-redo"></i> Mes actual
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $trabajosPeriodo }}</h3>
                    <p>Trabajos del Periodo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-car"></i>
                </div>
                <a href="{{ route('trabajos.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Bs {{ number_format($ingresosPeriodo, 2) }}</h3>
                    <p>Ingresos del Periodo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="{{ route('trabajos.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>Bs {{ number_format($comisionesPeriodo, 2) }}</h3>
                    <p>Comisiones del Periodo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <a href="{{ route('pagos.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>Bs {{ number_format($gastosPeriodo, 2) }}</h3>
                    <p>Gastos del Periodo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <a href="{{ route('gastos.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Utilidad Neta -->
    <div class="row">
        <div class="col-md-3">
            <div class="info-box {{ $utilidadNeta >= 0 ? 'bg-gradient-success' : 'bg-gradient-warning' }}">
                <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Utilidad Neta</span>
                    <span class="info-box-number">Bs {{ number_format($utilidadNeta, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas de Inventario Bajo -->
    @if($itemsBajoStock->count() > 0)
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Alertas de Inventario!</h5>
                    <p>Los siguientes items tienen stock bajo:</p>
                    <ul>
                        @foreach($itemsBajoStock as $item)
                            <li>
                                <strong>{{ $item->nombre }}</strong> - 
                                Stock: {{ $item->stock_actual }} {{ $item->unidad_medida }}(s) 
                                (Mínimo: {{ $item->stock_minimo }})
                                <a href="{{ route('inventarios.edit', $item->id_inventario) }}" class="btn btn-xs btn-warning ml-2">
                                    <i class="fas fa-edit"></i> Actualizar
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Gráficos y tablas -->
    <div class="row">
        <!-- Ingresos por tipo de servicio -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line"></i> Ingresos por Tipo de Servicio</h3>
                </div>
                <div class="card-body">
                    @if($ingresosPorServicio->count() > 0)
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Servicio</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-right">Ingresos</th>
                                    <th class="text-right">Comisiones</th>
                                    <th class="text-right">Promedio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ingresosPorServicio as $item)
                                    <tr>
                                        <td>{{ Str::limit($item->nombre, 20) }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-info">{{ $item->cantidad }}</span>
                                        </td>
                                        <td class="text-right">
                                            <strong class="text-success">Bs {{ number_format($item->total_ingresos, 2) }}</strong>
                                        </td>
                                        <td class="text-right">
                                            <span class="text-muted">Bs {{ number_format($item->total_comisiones, 2) }}</span>
                                        </td>
                                        <td class="text-right">
                                            <small>Bs {{ number_format($item->promedio_ingreso, 2) }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold bg-light">
                                    <td>TOTAL</td>
                                    <td class="text-center">{{ $ingresosPorServicio->sum('cantidad') }}</td>
                                    <td class="text-right text-success">Bs {{ number_format($ingresosPorServicio->sum('total_ingresos'), 2) }}</td>
                                    <td class="text-right">Bs {{ number_format($ingresosPorServicio->sum('total_comisiones'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <p class="text-muted">No hay trabajos registrados en el periodo seleccionado.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Servicios más solicitados -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Servicios Más Solicitados</h3>
                </div>
                <div class="card-body">
                    @if($serviciosMasSolicitados->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Servicio</th>
                                    <th>Cantidad</th>
                                    <th>Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalServicios = $serviciosMasSolicitados->sum('total');
                                @endphp
                                @foreach($serviciosMasSolicitados as $item)
                                    <tr>
                                        <td>{{ $item->nombre }}</td>
                                        <td><span class="badge badge-success">{{ $item->total }}</span></td>
                                        <td>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-success" style="width: {{ ($item->total / $totalServicios) * 100 }}%"></div>
                                            </div>
                                            <small>{{ number_format(($item->total / $totalServicios) * 100, 1) }}%</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No hay servicios registrados en el periodo seleccionado.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos trabajos -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock"></i> Últimos Trabajos Registrados</h3>
                    <div class="card-tools">
                        <a href="{{ route('trabajos.index') }}" class="btn btn-tool">
                            Ver todos <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($ultimosTrabajos->count() > 0)
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Fecha</th>
                                    <th>Placa</th>
                                    <th>Servicios</th>
                                    <th>Técnico</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimosTrabajos as $trabajo)
                                    <tr>
                                        <td>{{ $trabajo->id_trabajo }}</td>
                                        <td>{{ $trabajo->fecha_trabajo->format('d/m/Y') }}</td>
                                        <td><strong>{{ $trabajo->cliente ? $trabajo->cliente->placas : 'N/A' }}</strong></td>
                                        <td>
                                            <small>{{ $trabajo->trabajoServicios->count() }} servicio(s)</small>
                                        </td>
                                        <td>{{ $trabajo->empleado->nombre }}</td>
                                        <td>Bs {{ number_format($trabajo->total_cliente, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-3">
                            <p class="text-muted">No hay trabajos registrados aún.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    @vite('resources/css/adminlte-theme.css')
@stop

@section('js')
<script>
    document.addEventListener('click', function(e) {
        const target = e.target.closest('a[href*="logout"]');
        
        if (target && target.getAttribute('href').includes('logout')) {
            e.preventDefault();
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = target.getAttribute('href');
            
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '{{ csrf_token() }}';
            
            form.appendChild(token);
            document.body.appendChild(form);
            form.submit();
        }
    });
</script>
@stop