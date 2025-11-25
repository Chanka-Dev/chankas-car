@extends('adminlte::page')

@section('title', 'Editar Gasto - Chankas Car')

@section('content_header')
    <h1>Editar Gasto</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Edición</h3>
        </div>
        <form action="{{ route('gastos.update', $gasto->id_gasto) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha">Fecha del Gasto</label>
                            <input type="date" 
                                   class="form-control @error('fecha') is-invalid @enderror" 
                                   id="fecha" 
                                   name="fecha" 
                                   value="{{ old('fecha', $gasto->fecha->format('Y-m-d')) }}"
                                   required>
                            @error('fecha')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="monto">Monto (Bs)</label>
                            <input type="number" 
                                   class="form-control @error('monto') is-invalid @enderror" 
                                   id="monto" 
                                   name="monto" 
                                   step="0.01"
                                   min="0"
                                   value="{{ old('monto', $gasto->monto) }}"
                                   required>
                            @error('monto')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="concepto">Concepto</label>
                    <input type="text" 
                           class="form-control @error('concepto') is-invalid @enderror" 
                           id="concepto" 
                           name="concepto" 
                           value="{{ old('concepto', $gasto->concepto) }}"
                           list="conceptos-list"
                           required>
                    <datalist id="conceptos-list">
                        <option value="Luz">
                        <option value="Agua">
                        <option value="Internet">
                        <option value="Alquiler">
                        <option value="Herramientas">
                        <option value="Material de limpieza">
                        <option value="Repuestos">
                        <option value="Mantenimiento">
                        <option value="Combustible">
                        <option value="Otros">
                    </datalist>
                    @error('concepto')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción Detallada</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                              id="descripcion" 
                              name="descripcion" 
                              rows="3">{{ old('descripcion', $gasto->descripcion) }}</textarea>
                    @error('descripcion')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="comprobante">N° de Comprobante</label>
                            <input type="text" 
                                   class="form-control @error('comprobante') is-invalid @enderror" 
                                   id="comprobante" 
                                   name="comprobante" 
                                   value="{{ old('comprobante', $gasto->comprobante) }}">
                            @error('comprobante')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_empleado">Registrado por</label>
                            <select class="form-control @error('id_empleado') is-invalid @enderror" 
                                    id="id_empleado" 
                                    name="id_empleado">
                                <option value="">Sistema (sin asignar)</option>
                                @foreach($empleados as $empleado)
                                    <option value="{{ $empleado->id_empleado }}" 
                                        {{ old('id_empleado', $gasto->id_empleado) == $empleado->id_empleado ? 'selected' : '' }}>
                                        {{ $empleado->nombre }} {{ $empleado->apellido }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_empleado')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('gastos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@stop

@section('css')
    @vite('resources/css/adminlte-theme.css')
@stop