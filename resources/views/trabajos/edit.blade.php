@extends('adminlte::page')

@section('title', 'Editar Trabajo - Chankas Car')

@section('content_header')
    <h1>Editar Trabajo #{{ $trabajo->id_trabajo }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Edición</h3>
        </div>
        <form action="{{ route('trabajos.update', $trabajo->id_trabajo) }}" method="POST" id="form-trabajo">
            @csrf
            @method('PUT')
            <div class="card-body">
                <!-- Fechas -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_trabajo">Fecha de Trabajo</label>
                            <input type="date" 
                                   class="form-control @error('fecha_trabajo') is-invalid @enderror" 
                                   id="fecha_trabajo" 
                                   name="fecha_trabajo" 
                                   value="{{ old('fecha_trabajo', $trabajo->fecha_trabajo->format('Y-m-d')) }}"
                                   required>
                            @error('fecha_trabajo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_recepcion">Fecha de Recepción</label>
                            <input type="date" 
                                   class="form-control @error('fecha_recepcion') is-invalid @enderror" 
                                   id="fecha_recepcion" 
                                   name="fecha_recepcion" 
                                   value="{{ old('fecha_recepcion', $trabajo->fecha_recepcion->format('Y-m-d')) }}"
                                   required>
                            @error('fecha_recepcion')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_recalificacion">Fecha de Recalificación</label>
                            <input type="date" 
                                   class="form-control @error('fecha_recalificacion') is-invalid @enderror" 
                                   id="fecha_recalificacion" 
                                   name="fecha_recalificacion" 
                                   value="{{ old('fecha_recalificacion', $trabajo->fecha_recalificacion?->format('Y-m-d')) }}">
                            @error('fecha_recalificacion')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Opcional</small>
                        </div>
                    </div>
                </div>

                <!-- Cliente -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="placas">Placas del Vehículo</label>
                            <input type="text" 
                                   class="form-control @error('placas') is-invalid @enderror" 
                                   id="placas" 
                                   name="placas" 
                                   value="{{ old('placas', $trabajo->cliente?->placas) }}"
                                   style="text-transform: uppercase;">
                            @error('placas')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Opcional: Dejar vacío si no se tiene el dato</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">Teléfono/Celular</label>
                            <input type="text" 
                                   class="form-control @error('telefono') is-invalid @enderror" 
                                   id="telefono" 
                                   name="telefono" 
                                   value="{{ old('telefono', $trabajo->cliente?->telefono) }}">
                            @error('telefono')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted" id="info-telefono">Opcional</small>
                        </div>
                    </div>
                </div>

                <!-- Empleado -->
                <div class="form-group">
                    <label for="id_empleado">Técnico (Empleado)</label>
                    <select class="form-control @error('id_empleado') is-invalid @enderror" 
                            id="id_empleado" 
                            name="id_empleado" 
                            required>
                        <option value="">Seleccione un técnico...</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->id_empleado }}" 
                                    {{ old('id_empleado', $trabajo->id_empleado) == $empleado->id_empleado ? 'selected' : '' }}>
                                {{ $empleado->nombre }} {{ $empleado->apellido }} ({{ $empleado->cargo->nombre }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_empleado')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Servicios Realizados -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> Servicios Realizados</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" id="btn-agregar-servicio">
                                <i class="fas fa-plus"></i> Agregar Servicio
                            </button>
                        </div>

                        <div id="servicios-container">
                            <!-- Los servicios se cargarán aquí -->
                        </div>

                        <div class="alert alert-info" id="alert-sin-servicios" style="display: none;">
                            <i class="fas fa-info-circle"></i> No hay servicios agregados. Haga clic en "Agregar Servicio" para comenzar.
                        </div>

                        @error('servicios')
                            <div class="alert alert-danger">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Observaciones Generales -->
                <div class="form-group">
                    <label for="observaciones">Observaciones Generales</label>
                    <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                              id="observaciones" 
                              name="observaciones" 
                              rows="3"
                              placeholder="Observaciones generales del trabajo...">{{ old('observaciones', $trabajo->observaciones) }}</textarea>
                    @error('observaciones')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Resumen de Totales -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total a Cobrar al Cliente</span>
                                <span class="info-box-number" id="total-cliente">Bs 0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box bg-primary">
                            <span class="info-box-icon"><i class="fas fa-user"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Comisión Técnico</span>
                                <span class="info-box-number" id="total-tecnico">Bs 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Actualizar Trabajo
                </button>
                <a href="{{ route('trabajos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <!-- Template para nuevo servicio -->
    <template id="servicio-template">
        <div class="card card-outline card-secondary servicio-item mb-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tipo de Trabajo</label>
                            <select class="form-control servicio-select" name="servicios[INDEX][id_servicio]" required>
                                <option value="">Seleccione...</option>
                                @foreach($servicios as $servicio)
                                    <option value="{{ $servicio->id_servicio }}" 
                                            data-costo="{{ $servicio->costo }}"
                                            data-comision="{{ $servicio->comision }}">
                                        {{ $servicio->nombre }} - Bs {{ number_format($servicio->costo, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Cantidad</label>
                            <input type="number" class="form-control cantidad-input" name="servicios[INDEX][cantidad]" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Importe Cliente</label>
                            <input type="number" class="form-control importe-cliente-input" name="servicios[INDEX][importe_cliente]" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Importe Técnico</label>
                            <input type="number" class="form-control importe-tecnico-input" name="servicios[INDEX][importe_tecnico]" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Observaciones</label>
                            <input type="text" class="form-control" name="servicios[INDEX][observaciones]" placeholder="Opcional">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-block btn-sm btn-eliminar-servicio">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
@stop

@section('css')
    @vite('resources/css/adminlte-theme.css')
@stop

@section('js')
<script>
    $(document).ready(function() {
        let servicioIndex = 0;

        // Convertir placas a mayúsculas
        $('#placas').on('input', function() {
            $(this).val($(this).val().toUpperCase());
        });

        // Autocompletar teléfono cuando se escribe la placa
        let timeoutPlaca = null;
        $('#placas').on('input', function() {
            let placa = $(this).val().trim();
            let placasOriginal = '{{ $trabajo->cliente?->placas }}';
            
            // Limpiar timeout anterior
            clearTimeout(timeoutPlaca);
            
            if (placa.length >= 3) {
                // Esperar 500ms después de que el usuario deje de escribir
                timeoutPlaca = setTimeout(function() {
                    $.ajax({
                        url: '{{ route("trabajos.buscar-cliente") }}',
                        type: 'POST',
                        data: {
                            placas: placa,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.existe) {
                                $('#telefono').val(response.telefono);
                                if (placa === placasOriginal) {
                                    $('#info-telefono').html('<i class="fas fa-info-circle text-muted"></i> Teléfono actual del cliente');
                                } else {
                                    $('#info-telefono').html('<i class="fas fa-check-circle text-success"></i> Cliente encontrado - teléfono autocargado');
                                }
                            } else {
                                $('#telefono').val('');
                                $('#info-telefono').html('<i class="fas fa-info-circle text-info"></i> Cliente nuevo - ingrese el teléfono');
                            }
                        },
                        error: function() {
                            console.log('Error al buscar cliente');
                        }
                    });
                }, 500);
            } else {
                $('#telefono').val('');
                $('#info-telefono').text('Opcional');
            }
        });

        // Agregar servicio
        $('#btn-agregar-servicio').on('click', function() {
            let template = $('#servicio-template').html();
            template = template.replace(/INDEX/g, servicioIndex);
            $('#servicios-container').append(template);
            $('#alert-sin-servicios').hide();
            
            // Inicializar Select2 en el nuevo select de servicio
            let newSelect = $('#servicios-container .servicio-item').last().find('.servicio-select');
            newSelect.select2({
                theme: 'bootstrap4',
                placeholder: 'Buscar servicio...',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });
            
            servicioIndex++;
            calcularTotales();
        });

        // Eliminar servicio
        $(document).on('click', '.btn-eliminar-servicio', function() {
            $(this).closest('.servicio-item').remove();
            if ($('.servicio-item').length === 0) {
                $('#alert-sin-servicios').show();
            }
            calcularTotales();
        });

        // Auto-completar importes cuando se selecciona un servicio
        $(document).on('change', '.servicio-select', function() {
            let selectedOption = $(this).find('option:selected');
            let costo = selectedOption.data('costo');
            let comision = selectedOption.data('comision');
            let card = $(this).closest('.servicio-item');
            
            if (costo && confirm('¿Desea actualizar los importes con los valores del servicio seleccionado?')) {
                card.find('.importe-cliente-input').val(costo);
                card.find('.importe-tecnico-input').val(comision);
                calcularTotales();
            }
        });

        // Calcular totales al cambiar cantidad o importes
        $(document).on('input', '.cantidad-input, .importe-cliente-input, .importe-tecnico-input', function() {
            calcularTotales();
        });

        // Función para calcular totales
        function calcularTotales() {
            let totalCliente = 0;
            let totalTecnico = 0;

            $('.servicio-item').each(function() {
                let cantidad = parseFloat($(this).find('.cantidad-input').val()) || 0;
                let importeCliente = parseFloat($(this).find('.importe-cliente-input').val()) || 0;
                let importeTecnico = parseFloat($(this).find('.importe-tecnico-input').val()) || 0;

                totalCliente += cantidad * importeCliente;
                totalTecnico += cantidad * importeTecnico;
            });

            $('#total-cliente').text('Bs ' + totalCliente.toFixed(2));
            $('#total-tecnico').text('Bs ' + totalTecnico.toFixed(2));
        }

        // Validar que haya al menos un servicio al enviar
        $('#form-trabajo').on('submit', function(e) {
            if ($('.servicio-item').length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un servicio al trabajo.');
                return false;
            }
        });

        // Cargar servicios existentes
        @foreach($trabajo->trabajoServicios as $ts)
            let template{{ $loop->index }} = $('#servicio-template').html();
            template{{ $loop->index }} = template{{ $loop->index }}.replace(/INDEX/g, servicioIndex);
            $('#servicios-container').append(template{{ $loop->index }});
            
            let card{{ $loop->index }} = $('.servicio-item').last();
            card{{ $loop->index }}.find('.servicio-select').val('{{ $ts->id_servicio }}');
            card{{ $loop->index }}.find('.cantidad-input').val('{{ $ts->cantidad }}');
            card{{ $loop->index }}.find('.importe-cliente-input').val('{{ $ts->importe_cliente }}');
            card{{ $loop->index }}.find('.importe-tecnico-input').val('{{ $ts->importe_tecnico }}');
            card{{ $loop->index }}.find('input[name*="observaciones"]').val('{{ $ts->observaciones }}');
            
            // Inicializar Select2 en este servicio existente
            card{{ $loop->index }}.find('.servicio-select').select2({
                theme: 'bootstrap4',
                placeholder: 'Buscar servicio...',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });
            
            servicioIndex++;
        @endforeach

        if ($('.servicio-item').length > 0) {
            $('#alert-sin-servicios').hide();
        }

        calcularTotales();
    });
</script>
@stop

@section('css')
    @vite('resources/css/adminlte-theme.css')
    <style>
        /* Estilos mejorados para Select2 - apariencia de desplegable */
        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            background-color: #fff;
            cursor: pointer;
        }
        
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            line-height: calc(2.25rem) !important;
            padding-left: 0;
            color: #495057;
        }
        
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            height: calc(2.25rem) !important;
            position: absolute;
            top: 1px;
            right: 1px;
            width: 20px;
        }
        
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow b {
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
        .select2-container--bootstrap4 .select2-selection--single:hover {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        /* Estado focus */
        .select2-container--bootstrap4.select2-container--focus .select2-selection--single {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        /* Placeholder más visible */
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
            color: #6c757d;
            font-style: italic;
        }
    </style>
@stop