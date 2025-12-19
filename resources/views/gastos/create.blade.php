@extends('adminlte::page')

@section('title', 'Registrar Gasto - Chankas Car')

@section('content_header')
    <h1>Registrar Nuevo Gasto</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Gasto</h3>
            @can('admin')
                <div class="card-tools">
                    <a href="{{ route('tipos-gastos.index') }}" class="btn btn-sm btn-info" title="Gestionar tipos de gastos">
                        <i class="fas fa-tags"></i> Gestionar Tipos
                    </a>
                </div>
            @endcan
        </div>
        <form action="{{ route('gastos.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha">Fecha del Gasto</label>
                            <input type="date" 
                                   class="form-control @error('fecha') is-invalid @enderror" 
                                   id="fecha" 
                                   name="fecha" 
                                   value="{{ old('fecha', date('Y-m-d')) }}"
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
                                   placeholder="0.00"
                                   value="{{ old('monto') }}"
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
                            <div class="input-group">
                                <select class="form-control select2-concepto @error('concepto') is-invalid @enderror" 
                                        id="concepto" 
                                        name="concepto" 
                                        data-placeholder="Selecciona un concepto o escribe uno nuevo..."
                                        required>
                                    <option value=""></option>
                                    @foreach($conceptos as $c)
                                        <option value="{{ $c }}" {{ old('concepto') == $c ? 'selected' : '' }}>
                                            {{ $c }}
                                        </option>
                                    @endforeach
                                </select>
                                @can('admin')
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalNuevoTipo" title="Crear nuevo tipo de gasto">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @endcan
                            </div>
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
                              rows="3"
                              placeholder="Detalles adicionales del gasto...">{{ old('descripcion') }}</textarea>
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
                                   placeholder="Ej: Factura #12345" 
                                   value="{{ old('comprobante') }}">
                            @error('comprobante')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Opcional: número de factura o recibo</small>
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
                                        {{ (old('id_empleado', $empleadoActual) == $empleado->id_empleado) ? 'selected' : '' }}>
                                        {{ $empleado->nombre }} {{ $empleado->apellido }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_empleado')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                @if($empleadoActual)
                                    <i class="fas fa-info-circle text-success"></i> Autocompletado con tu usuario
                                @else
                                    Opcional: empleado que realizó el gasto
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Registrar Gasto
                </button>
                <a href="{{ route('gastos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <!-- Modal para crear nuevo tipo de gasto -->
    @can('admin')
    <div class="modal fade" id="modalNuevoTipo" tabindex="-1" role="dialog" aria-labelledby="modalNuevoTipoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="modalNuevoTipoLabel">
                        <i class="fas fa-plus-circle"></i> Crear Nuevo Tipo de Gasto
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nuevo_tipo_nombre">Nombre del Tipo <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="nuevo_tipo_nombre" 
                               placeholder="Ej: SERVICIOS BÁSICOS" 
                               maxlength="150"
                               required>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Se convertirá automáticamente a mayúsculas
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="nuevo_tipo_descripcion">Descripción (opcional)</label>
                        <textarea class="form-control" 
                                  id="nuevo_tipo_descripcion" 
                                  rows="2" 
                                  placeholder="Descripción del tipo de gasto..."
                                  maxlength="500"></textarea>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-lightbulb"></i> El tipo se creará como <strong>Activo</strong> y estará disponible inmediatamente.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-success" id="btnGuardarTipo">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endcan
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
                minimumResultsForSearch: 0,
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

            // Modal para crear nuevo tipo de gasto
            $('#btnGuardarTipo').on('click', function() {
                const nombre = $('#nuevo_tipo_nombre').val().trim();
                const descripcion = $('#nuevo_tipo_descripcion').val().trim();
                
                if (!nombre) {
                    Swal.fire('Error', 'El nombre del tipo de gasto es obligatorio', 'error');
                    return;
                }

                // Deshabilitar botón mientras se procesa
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

                $.ajax({
                    url: '{{ route("tipos-gastos.store") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        nombre: nombre,
                        descripcion: descripcion,
                        activo: 1
                    },
                    success: function(response) {
                        // Cerrar modal
                        $('#modalNuevoTipo').modal('hide');
                        
                        // Agregar nueva opción al select
                        const newOption = new Option(nombre, nombre, true, true);
                        $('#concepto').append(newOption).trigger('change');
                        
                        // Limpiar formulario del modal
                        $('#nuevo_tipo_nombre').val('');
                        $('#nuevo_tipo_descripcion').val('');
                        
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: '¡Tipo de gasto creado!',
                            text: 'El nuevo tipo ha sido agregado exitosamente',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        let mensaje = 'Error al crear el tipo de gasto';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            mensaje = Object.values(xhr.responseJSON.errors)[0][0];
                        }
                        Swal.fire('Error', mensaje, 'error');
                    },
                    complete: function() {
                        $('#btnGuardarTipo').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
                    }
                });
            });

            // Limpiar modal al cerrarlo
            $('#modalNuevoTipo').on('hidden.bs.modal', function () {
                $('#nuevo_tipo_nombre').val('');
                $('#nuevo_tipo_descripcion').val('');
            });

            // Convertir nombre a mayúsculas automáticamente
            $('#nuevo_tipo_nombre').on('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
@stop