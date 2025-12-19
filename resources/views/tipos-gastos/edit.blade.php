@extends('layouts.base')

@section('title', 'Editar Tipo de Gasto - Chankas Car')

@section('content_header')
    <h1>Editar Tipo de Gasto</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-edit"></i> Formulario de Edición
            </h3>
        </div>
        <form action="{{ route('tipos-gastos.update', $tiposGasto->id_tipo_gasto) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="nombre">Nombre del Tipo de Gasto <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="{{ old('nombre', $tiposGasto->nombre) }}"
                                   placeholder="Ej: Servicios Básicos, Material de Oficina, etc."
                                   maxlength="150"
                                   required
                                   autofocus>
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            @if($tiposGasto->tieneGastos())
                                <small class="form-text text-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>Advertencia:</strong> Este tipo tiene {{ \App\Models\GastoTaller::where('concepto', $tiposGasto->nombre)->count() }} gasto(s) asociado(s). 
                                    Al cambiar el nombre, los gastos antiguos mantendrán el nombre anterior.
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="activo">Estado <span class="text-danger">*</span></label>
                            <select class="form-control @error('activo') is-invalid @enderror" 
                                    id="activo" 
                                    name="activo" 
                                    required>
                                <option value="1" {{ old('activo', $tiposGasto->activo) == '1' ? 'selected' : '' }}>
                                    Activo
                                </option>
                                <option value="0" {{ old('activo', $tiposGasto->activo) == '0' ? 'selected' : '' }}>
                                    Inactivo
                                </option>
                            </select>
                            @error('activo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Los tipos inactivos no aparecen en nuevos registros
                            </small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                              id="descripcion" 
                              name="descripcion" 
                              rows="3"
                              maxlength="500"
                              placeholder="Descripción opcional del tipo de gasto...">{{ old('descripcion', $tiposGasto->descripcion) }}</textarea>
                    @error('descripcion')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                @if($tiposGasto->tieneGastos())
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Importante:</strong> Este tipo de gasto tiene registros asociados, por lo que no puede ser eliminado. 
                        Puedes desactivarlo si no deseas que se use en nuevos registros.
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="callout callout-info">
                            <h5><i class="fas fa-calendar"></i> Creado:</h5>
                            <p>{{ $tiposGasto->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-info">
                            <h5><i class="fas fa-sync"></i> Última actualización:</h5>
                            <p>{{ $tiposGasto->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('tipos-gastos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <a href="{{ route('tipos-gastos.show', $tiposGasto->id_tipo_gasto) }}" class="btn btn-info">
                    <i class="fas fa-eye"></i> Ver Detalles
                </a>
            </div>
        </form>
    </div>
@stop

@section('css')
    <style>
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
    </style>
@stop

@push('scripts')
    <script>
        // Convertir nombre a mayúsculas automáticamente (opcional)
        $('#nombre').on('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
@endpush
