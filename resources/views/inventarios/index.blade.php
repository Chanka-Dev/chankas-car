@extends('layouts.base')

@section('title', 'Inventario - Chankas Car')

@section('content_header')
    <h1>Gestión de Inventario</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Items en Inventario</h3>
            <div class="card-tools">
                @canManage
                    <a href="{{ route('inventarios.create') }}" class="btn btn-success btn-sm text-white">
                        <i class="fas fa-plus"></i> Nuevo Item
                    </a>
                @endcanManage
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="inventarios-table" class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Unidad</th>
                            <th>Stock Actual</th>
                            <th>Stock Mínimo</th>
                            <th>Tipo Stock</th>
                            <th>Precio Compra</th>
                            <th>Precio Venta</th>
                            <th>Proveedor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventarios as $item)
                            <tr class="{{ $item->stock_actual <= $item->stock_minimo ? 'table-warning' : '' }}">
                                <td>{{ $item->id_inventario }}</td>
                                <td><strong>{{ $item->nombre }}</strong></td>
                                <td>{{ $item->descripcion ? Str::limit($item->descripcion, 30) : '-' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst($item->unidad_medida) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($item->stock_actual <= $item->stock_minimo)
                                        <span class="badge badge-danger">{{ $item->stock_actual }}</span>
                                        <i class="fas fa-exclamation-triangle text-warning" title="Stock bajo"></i>
                                    @else
                                        <span class="badge badge-success">{{ $item->stock_actual }}</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->stock_minimo }}</td>
                                <td>
                                    @if($item->tipo_stock == 'contable')
                                        <span class="badge badge-primary">Contable</span>
                                    @else
                                        <span class="badge badge-secondary">Pregunta</span>
                                    @endif
                                </td>
                                <td class="text-right">Bs {{ number_format($item->precio_compra, 2) }}</td>
                                <td class="text-right">Bs {{ number_format($item->precio_venta, 2) }}</td>
                                <td>{{ $item->proveedor ? $item->proveedor->nombre : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('inventarios.edit', $item->id_inventario) }}" class="btn btn-primary btn-xs">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form id="delete-form-{{ $item->id_inventario }}" action="{{ route('inventarios.destroy', $item->id_inventario) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-xs" onclick="confirmarEliminacion('delete-form-{{ $item->id_inventario }}', '{{ $item->nombre }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Alertas de Stock Bajo -->
    @php
        $stockBajo = $inventarios->filter(function($item) {
            return $item->stock_actual <= $item->stock_minimo;
        });
    @endphp

    @if($stockBajo->count() > 0)
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Alertas de Stock Bajo</h3>
            </div>
            <div class="card-body">
                <p>Los siguientes items tienen stock bajo o agotado:</p>
                <ul>
                    @foreach($stockBajo as $item)
                        <li>
                            <strong>{{ $item->nombre }}</strong> - 
                            Stock actual: {{ $item->stock_actual }} {{ $item->unidad_medida }}(s) 
                            (Mínimo requerido: {{ $item->stock_minimo }})
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@stop

@push('scripts')
    <style>
        .table-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }
    </style>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#inventarios-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                },
                "order": [[4, "asc"]] // Ordenar por stock actual ascendente
            });
        });
    </script>
@endpush