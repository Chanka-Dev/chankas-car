@extends('adminlte::page')

@section('title', 'Crear Cargo - Chankas Car')

@section('content_header')
    <h1>Crear Nuevo Cargo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Cargo</h3>
        </div>
        <form action="{{ route('cargos.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="nombre">Nombre del Cargo</label>
                    <input type="text" 
                           class="form-control @error('nombre') is-invalid @enderror" 
                           id="nombre" 
                           name="nombre" 
                           placeholder="Ej: Mecánico, Electricista, Gerente" 
                           value="{{ old('nombre') }}"
                           required>
                    @error('nombre')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('cargos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@stop

@section('css')
    @vite('resources/css/adminlte-theme.css')
@stop