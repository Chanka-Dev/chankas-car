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

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="concepto">Concepto</label>
                            <select class="form-control select2-concepto @error('concepto') is-invalid @enderror" 
                                    id="concepto" 
                                    name="concepto" 
                                    data-placeholder="Selecciona un concepto o escribe uno nuevo..."
                                    required>
                                <option value=""></option>
                                @foreach($conceptos as $c)
                                    <option value="{{ $c }}" {{ old('concepto', $gasto->concepto) == $c ? 'selected' : '' }}>
                                        {{ $c }}
                                    </option>
                                @endforeach
                            </select>
                            @error('concepto')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-lightbulb"></i> Puedes seleccionar un concepto existente o escribir uno nuevo
                            </small>
                        </div>
                    </div>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    @vite('resources/css/adminlte-theme.css')
    <style>
        /* Hacer que Select2 se vea más como un campo desplegable tradicional */
        .select2-concepto + .select2-container .select2-selection {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            min-height: 38px;
            padding: 0.375rem 0.75rem;
            background-color: #fff;
            cursor: pointer;
        }
        
        /* Asegurar que la flecha sea visible y clara */
        .select2-concepto + .select2-container .select2-selection__arrow {
            height: 36px;
            position: absolute;
            top: 1px;
            right: 1px;
            width: 20px;
        }
        
        .select2-concepto + .select2-container .select2-selection__arrow b {
            border-color: #495057 transparent transparent transparent;
            border-style: solid;
            border-width: 5px 4px 0 4px;
            height: 0;
            left: 50%;
            margin-left: -4px;
            margin-top: -2px;
            position: absolute;
            top: 50%;
            width: 0;
        }
        
        /* Estado hover */
        .select2-concepto + .select2-container .select2-selection:hover {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        /* Estado focus */
        .select2-concepto + .select2-container--focus .select2-selection {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        /* Hacer el placeholder más visible */
        .select2-concepto + .select2-container .select2-selection__placeholder {
            color: #6c757d;
            font-style: italic;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2 para conceptos
            $('.select2-concepto').select2({
                theme: 'bootstrap4',
                tags: true,
                placeholder: "Selecciona un concepto o escribe uno nuevo...",
                allowClear: true,
                language: {
                    noResults: function() {
                        return "No se encontró el concepto. Escribe para crear uno nuevo.";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                },
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term + ' (nuevo)',
                        newTag: true
                    };
                }
            });
        });
    </script>
@stop