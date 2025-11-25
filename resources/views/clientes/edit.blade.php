@extends('adminlte::page')

@section('title', 'Editar Cliente - Chankas Car')

@section('content_header')
    <h1>Editar Cliente</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Edición</h3>
        </div>
        <form action="{{ route('clientes.update', $cliente->id_cliente) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="placas">Placas del Vehículo</label>
                            <input type="text" 
                                   class="form-control @error('placas') is-invalid @enderror" 
                                   id="placas" 
                                   name="placas" 
                                   value="{{ old('placas', $cliente->placas) }}"
                                   style="text-transform: uppercase;"
                                   required>
                            @error('placas')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">Teléfono/Celular</label>
                            <input type="text" 
                                   class="form-control @error('telefono') is-invalid @enderror" 
                                   id="telefono" 
                                   name="telefono" 
                                   value="{{ old('telefono', $cliente->telefono) }}">
                            @error('telefono')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                @if($cliente->trabajos->count() > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Información:</strong> Este cliente tiene {{ $cliente->trabajos->count() }} trabajo(s) registrado(s).
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Actualizar
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