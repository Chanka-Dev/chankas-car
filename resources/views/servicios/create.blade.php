@extends('adminlte::page')

@section('title', 'Crear Servicio - Chankas Car')

@section('content_header')
    <h1>Crear Nuevo Servicio</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Servicio</h3>
        </div>
        <form action="{{ route('servicios.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="nombre">Nombre del Servicio</label>
                    <input type="text" 
                           class="form-control @error('nombre') is-invalid @enderror" 
                           id="nombre" 
                           name="nombre" 
                           placeholder="Ej: Habilitación Simple, Recalificación" 
                           value="{{ old('nombre') }}"
                           required>
                    @error('nombre')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="costo">Costo Base (Bs)</label>
                            <input type="number" 
                                   class="form-control @error('costo') is-invalid @enderror" 
                                   id="costo" 
                                   name="costo" 
                                   step="0.01"
                                   min="0"
                                   placeholder="100.00" 
                                   value="{{ old('costo') }}"
                                   required>
                            @error('costo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Precio sugerido del servicio</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="comision">Comisión Base (Bs)</label>
                            <input type="number" 
                                   class="form-control @error('comision') is-invalid @enderror" 
                                   id="comision" 
                                   name="comision" 
                                   step="0.01"
                                   min="0"
                                   placeholder="10.00" 
                                   value="{{ old('comision', 0) }}"
                                   required>
                            @error('comision')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Comisión sugerida para el técnico</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('servicios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@stop

@section('css')
    @vite('resources/css/adminlte-theme.css')
@stop