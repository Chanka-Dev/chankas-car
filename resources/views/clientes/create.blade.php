@extends('adminlte::page')

@section('title', 'Crear Cliente - Chankas Car')

@section('content_header')
    <h1>Registrar Nuevo Cliente</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Cliente</h3>
        </div>
        <form action="{{ route('clientes.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="placas">Placas del Vehículo</label>
                            <input type="text" 
                                   class="form-control @error('placas') is-invalid @enderror" 
                                   id="placas" 
                                   name="placas" 
                                   placeholder="Ej: 4440FPX, 2715FUG" 
                                   value="{{ old('placas') }}"
                                   style="text-transform: uppercase;"
                                   required>
                            @error('placas')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Ingrese las placas del vehículo</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">Teléfono/Celular</label>
                            <input type="text" 
                                   class="form-control @error('telefono') is-invalid @enderror" 
                                   id="telefono" 
                                   name="telefono" 
                                   placeholder="Ej: 73478728" 
                                   value="{{ old('telefono') }}">
                            @error('telefono')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Opcional: número de contacto del cliente</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@stop

@section('css')
    @vite('resources/css/adminlte-theme.css')
@stop

@section('js')
<script>
    // Convertir placas a mayúsculas automáticamente
    document.getElementById('placas').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@stop