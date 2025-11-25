@extends('adminlte::page')

@section('title', 'Dashboard - Chankas Car')

@section('adminlte_css_pre')
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.ico') }}?v=2">
@stop

@section('content_header')
    <h1>Panel de Control</h1>
@stop

@section('content')
    <!-- Tarjetas de estadísticas -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $trabajosMesActual }}</h3>
                    <p>Trabajos Este Mes</p>
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
                    <h3>Bs {{ number_format($ingresosMesActual, 2) }}</h3>
                    <p>Ingresos Este Mes</p>
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
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalEmpleados }}</h3>
                    <p>Empleados Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('empleados.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalClientes }}</h3>
                    <p>Clientes Registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <a href="{{ route('clientes.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Resumen Financiero -->
    <div class="row">
        <div class="col-md-3">
            <div class="info-box bg-gradient-success">
                <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Ingresos</span>
                    <span class="info-box-number">Bs {{ number_format($ingresosMesActual, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box bg-gradient-primary">
                <span class="info-box-icon"><i class="fas fa-hand-holding-usd"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Comisiones</span>
                    <span class="info-box-number">Bs {{ number_format($comisionesMesActual, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box bg-gradient-danger">
                <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Gastos</span>
                    <span class="info-box-number">Bs {{ number_format($gastosMesActual, 2) }}</span>
                </div>
            </div>
        </div>

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
                        <p class="text-muted">No hay trabajos registrados este mes.</p>
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
                        <p class="text-muted">No hay servicios registrados este mes.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Gastos por Concepto -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-money-bill-wave"></i> Gastos por Concepto</h3>
                    <div class="card-tools">
                        <a href="{{ route('gastos.index') }}" class="btn btn-tool">
                            Ver todos <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($gastosPorConcepto->count() > 0)
                        <table class="table table-sm">
                            <tbody>
                                @foreach($gastosPorConcepto as $gasto)
                                    <tr>
                                        <td>{{ $gasto->concepto }}</td>
                                        <td class="text-right">
                                            <span class="badge badge-danger">Bs {{ number_format($gasto->total, 2) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td>TOTAL GASTOS</td>
                                    <td class="text-right">
                                        <span class="badge badge-danger">Bs {{ number_format($gastosPorConcepto->sum('total'), 2) }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <p class="text-muted">No hay gastos registrados este mes.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Últimos trabajos -->
        <div class="col-md-6">
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