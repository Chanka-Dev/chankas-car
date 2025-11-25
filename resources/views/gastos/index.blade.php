@extends('adminlte::page')

@section('title', 'Gastos del Taller - Chankas Car')

@section('content_header')
    <h1>Gastos del Taller</h1>
@stop

@section('content')
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
                $gastosMesActual = $gastos->filter(function($gasto) {
                    return $gasto->fecha->month == date('m') && $gasto->fecha->year == date('Y');
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
                <table id="gastos-table" class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Concepto</th>
                            <th>Descripción</th>
                            <th>Monto (Bs)</th>
                            <th>Comprobante</th>
                            <th>Registrado por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gastos as $gasto)
                            <tr>
                                <td>{{ $gasto->id_gasto }}</td>
                                <td>{{ $gasto->fecha->format('d/m/Y') }}</td>
                                <td><strong>{{ $gasto->concepto }}</strong></td>
                                <td>{{ $gasto->descripcion ? Str::limit($gasto->descripcion, 40) : '-' }}</td>
                                <td class="text-right">
                                    <span class="badge badge-danger">Bs {{ number_format($gasto->monto, 2) }}</span>
                                </td>
                                <td>{{ $gasto->comprobante ?? 'N/A' }}</td>
                                <td>{{ $gasto->empleado ? $gasto->empleado->nombre . ' ' . $gasto->empleado->apellido : 'Sistema' }}</td>
                                <td>
                                    <a href="{{ route('gastos.edit', $gasto->id_gasto) }}" class="btn btn-primary btn-xs">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('gastos.destroy', $gasto->id_gasto) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('¿Está seguro de eliminar este gasto?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-right">TOTAL GENERAL:</td>
                            <td class="text-right">
                                <span class="badge badge-danger">Bs {{ number_format($gastos->sum('monto'), 2) }}</span>
                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
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
                                        <td>{{ $concepto }}</td>
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
                        @foreach($gastos->take(5) as $gasto)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $gasto->concepto }}</strong><br>
                                    <small class="text-muted">{{ $gasto->fecha->format('d/m/Y') }}</small>
                                </div>
                                <span class="badge badge-danger badge-pill">Bs {{ number_format($gasto->monto, 2) }}</span>
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
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#gastos-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                },
                "order": [[1, "desc"]] // Ordenar por fecha descendente
            });
        });
    </script>
@stop