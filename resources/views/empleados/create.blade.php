@extends('adminlte::page')

@section('title', 'Crear Empleado - Chankas Car')

@section('content_header')
    <h1>Crear Nuevo Empleado</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Empleado</h3>
        </div>
        <form action="{{ route('empleados.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ci">Cédula de Identidad (CI)</label>
                            <input type="text" 
                                   class="form-control @error('ci') is-invalid @enderror" 
                                   id="ci" 
                                   name="ci" 
                                   placeholder="Ej: 1234567 LP" 
                                   value="{{ old('ci') }}"
                                   required>
                            @error('ci')
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
                                   placeholder="Ej: 73478728" 
                                   value="{{ old('telefono') }}">
                            @error('telefono')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" 
                                   name="nombre" 
                                   placeholder="Ej: Francisco" 
                                   value="{{ old('nombre') }}"
                                   required>
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido">Apellido</label>
                            <input type="text" 
                                   class="form-control @error('apellido') is-invalid @enderror" 
                                   id="apellido" 
                                   name="apellido" 
                                   placeholder="Ej: Gonzales Contreras" 
                                   value="{{ old('apellido') }}"
                                   required>
                            @error('apellido')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_cargo">Cargo</label>
                    <select class="form-control @error('id_cargo') is-invalid @enderror" 
                            id="id_cargo" 
                            name="id_cargo" 
                            required>
                        <option value="">Seleccione un cargo...</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->id_cargo }}" {{ old('id_cargo') == $cargo->id_cargo ? 'selected' : '' }}>
                                {{ $cargo->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_cargo')
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
                <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@stop

@section('css')
    @vite('resources/css/adminlte-theme.css')
@stop