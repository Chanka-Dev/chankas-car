@extends('layouts.base')

@section('title', 'Crear Tipo de Gasto - Chankas Car')

@section('content_header')
    <h1>Crear Nuevo Tipo de Gasto</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-plus"></i> Formulario de Nuevo Tipo
            </h3>
        </div>
        <form action="{{ route('tipos-gastos.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="nombre">Nombre del Tipo de Gasto <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="{{ old('nombre') }}"
                                   placeholder="Ej: Servicios Básicos, Material de Oficina, etc."
                                   maxlength="150"
                                   required
                                   autofocus>
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Este nombre aparecerá en el formulario de gastos
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="activo">Estado <span class="text-danger">*</span></label>
                            <select class="form-control @error('activo') is-invalid @enderror" 
                                    id="activo" 
                                    name="activo" 
                                    required>
                                <option value="1" {{ old('activo', '1') == '1' ? 'selected' : '' }}>
                                    Activo
                                </option>
                                <option value="0" {{ old('activo') == '0' ? 'selected' : '' }}>
                                    Inactivo
                                </option>
                            </select>
                            @error('activo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
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
                              placeholder="Descripción opcional del tipo de gasto...">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-lightbulb"></i> Agrega detalles sobre cuándo usar este tipo de gasto
                    </small>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Nota:</strong> Una vez creado, este tipo de gasto estará disponible para selección al registrar gastos del taller.
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Tipo de Gasto
                </button>
                <a href="{{ route('tipos-gastos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
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
