@extends('layouts.base')

@section('title', 'Detalles del Tipo de Gasto - Chankas Car')

@section('content_header')
    <h1>Detalles del Tipo de Gasto</h1>
@stop

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Información del Tipo
            </h3>
            <div class="card-tools">
                <a href="{{ route('tipos-gastos.edit', $tiposGasto->id_tipo_gasto) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('tipos-gastos.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">ID:</dt>
                        <dd class="col-sm-8">{{ $tiposGasto->id_tipo_gasto }}</dd>

                        <dt class="col-sm-4">Nombre:</dt>
                        <dd class="col-sm-8"><strong>{{ $tiposGasto->nombre }}</strong></dd>

                        <dt class="col-sm-4">Estado:</dt>
                        <dd class="col-sm-8">
                            @if($tiposGasto->activo)
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Activo
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-ban"></i> Inactivo
                                </span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Descripción:</dt>
                        <dd class="col-sm-8">{{ $tiposGasto->descripcion ?? 'Sin descripción' }}</dd>
                    </dl>
                </div>

                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-5">Creado:</dt>
                        <dd class="col-sm-7">{{ $tiposGasto->created_at->format('d/m/Y H:i:s') }}</dd>

                        <dt class="col-sm-5">Última actualización:</dt>
                        <dd class="col-sm-7">{{ $tiposGasto->updated_at->format('d/m/Y H:i:s') }}</dd>

                        <dt class="col-sm-5">Gastos registrados:</dt>
                        <dd class="col-sm-7">
                            @php
                                $count = \App\Models\GastoTaller::where('concepto', $tiposGasto->nombre)->count();
                            @endphp
                            <span class="badge badge-info">{{ $count }}</span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Gastos Relacionados -->
    @if($gastosRelacionados->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-receipt"></i> Últimos 10 Gastos con este Tipo
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="10%">Fecha</th>
                                <th width="20%">Descripción</th>
                                <th width="12%" class="text-right">Monto</th>
                                <th width="15%">Registrado por</th>
                                <th width="12%">Comprobante</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gastosRelacionados as $gasto)
                                <tr>
                                    <td>{{ $gasto->fecha->format('d/m/Y') }}</td>
                                    <td>{{ Str::limit($gasto->descripcion, 50) ?? '-' }}</td>
                                    <td class="text-right">
                                        <strong>Bs. {{ number_format($gasto->monto, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($gasto->empleado)
                                            {{ $gasto->empleado->nombre }} {{ $gasto->empleado->apellido }}
                                        @else
                                            <span class="text-muted">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td>{{ $gasto->comprobante ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-right">Total (últimos 10):</th>
                                <th class="text-right">
                                    Bs. {{ number_format($gastosRelacionados->sum('monto'), 2) }}
                                </th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @php
                    $totalGastos = \App\Models\GastoTaller::where('concepto', $tiposGasto->nombre)->count();
                @endphp

                @if($totalGastos > 10)
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i> 
                        Mostrando los últimos 10 de <strong>{{ $totalGastos }} gastos totales</strong> con este tipo.
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> 
                    Este tipo de gasto aún no ha sido utilizado en ningún registro.
                </div>
            </div>
        </div>
    @endif

    <!-- Opciones de eliminación -->
    @if(!$tiposGasto->tieneGastos())
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle"></i> Zona de Peligro
                </h3>
            </div>
            <div class="card-body">
                <p>
                    <i class="fas fa-info-circle"></i> 
                    Este tipo de gasto no tiene registros asociados. Puedes eliminarlo si ya no lo necesitas.
                </p>
                <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">
                    <i class="fas fa-trash"></i> Eliminar Tipo de Gasto
                </button>
            </div>
        </div>

        <form id="delete-form" 
              action="{{ route('tipos-gastos.destroy', $tiposGasto->id_tipo_gasto) }}" 
              method="POST" 
              style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @else
        <div class="card card-warning">
            <div class="card-body">
                <i class="fas fa-lock"></i> 
                <strong>Este tipo de gasto no puede ser eliminado</strong> porque tiene registros asociados. 
                Puedes desactivarlo desde el botón <a href="{{ route('tipos-gastos.edit', $tiposGasto->id_tipo_gasto) }}">Editar</a>.
            </div>
        </div>
    @endif
@stop

@section('css')
    <style>
        .table td {
            vertical-align: middle;
        }
        dl dt {
            font-weight: 600;
            color: #495057;
        }
    </style>
@stop

@push('scripts')
    <script>
        function confirmarEliminacion() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        }
    </script>
@endpush
