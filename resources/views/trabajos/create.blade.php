@extends('adminlte::page')

@section('title', 'Registrar Trabajo - Chankas Car')

@section('content_header')
    <h1>Registrar Nuevo Trabajo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Trabajo</h3>
        </div>
        <form action="{{ route('trabajos.store') }}" method="POST" id="form-trabajo">
            @csrf
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
                                   value="{{ old('fecha_trabajo', date('Y-m-d')) }}"
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
                                   value="{{ old('fecha_recepcion') }}"
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
                                   value="{{ old('fecha_recalificacion') }}">
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
                                   placeholder="Ej: 4440FPX (Opcional)" 
                                   value="{{ old('placas') }}"
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
                                   placeholder="Ej: 73478728 (Opcional)" 
                                   value="{{ old('telefono') }}">
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
                            <option value="{{ $empleado->id_empleado }}" {{ old('id_empleado') == $empleado->id_empleado ? 'selected' : '' }}>
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
                            <!-- Los servicios se agregarán aquí dinámicamente -->
                        </div>

                        <div class="alert alert-info" id="alert-sin-servicios">
                            <i class="fas fa-info-circle"></i> No hay servicios agregados. Haga clic en "Agregar Servicio" para comenzar.
                        </div>

                        @error('servicios')
                            <div class="alert alert-danger">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>
                </div>
                
                <!-- Piezas/Inventario Usado -->
<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-cogs"></i> Piezas/Inventario Usado (Opcional)</h3>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <button type="button" class="btn btn-warning btn-sm" id="btn-agregar-pieza-trabajo">
                <i class="fas fa-plus"></i> Agregar Pieza
            </button>
            <button type="button" class="btn btn-info btn-sm" id="btn-cargar-piezas-servicio" disabled>
                <i class="fas fa-magic"></i> Cargar Piezas del Servicio
            </button>
            <small class="text-muted ml-2">Opcional: Registre qué piezas se usaron en este trabajo</small>
        </div>

        <div id="piezas-trabajo-container">
            <!-- Las piezas se agregarán aquí -->
        </div>

        <div class="alert alert-info" id="alert-sin-piezas-trabajo">
            <i class="fas fa-info-circle"></i> No hay piezas registradas. Esto es opcional.
        </div>

        @error('piezas')
            <div class="alert alert-danger">
                <strong>{{ $message }}</strong>
            </div>
        @enderror
    </div>
</div>

<!-- Template para nueva pieza en trabajo -->
<template id="pieza-trabajo-template">
    <div class="card card-outline card-warning pieza-trabajo-item mb-2">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Pieza/Item</label>
                        <select class="form-control pieza-trabajo-select" name="piezas[PINDEX][id_inventario]" required>
                            <option value="">Seleccione una pieza...</option>
                            @php
                                $inventarios = \App\Models\Inventario::orderBy('nombre')->get();
                            @endphp
                            @foreach($inventarios as $item)
                                <option value="{{ $item->id_inventario }}"
                                        data-unidad="{{ $item->unidad_medida }}"
                                        data-stock="{{ $item->stock_actual }}"
                                        data-precio="{{ $item->precio_venta }}"
                                        data-tipo="{{ $item->tipo_stock }}">
                                    {{ $item->nombre }} (Stock: {{ $item->stock_actual }} {{ $item->unidad_medida }}s)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Cantidad Usada</label>
                        <input type="number" class="form-control cantidad-pieza-input" 
                               name="piezas[PINDEX][cantidad_usada]" 
                               value="1" min="0.01" step="0.01" required>
                        <small class="text-muted unidad-pieza">unidad(es)</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Precio Unitario</label>
                        <input type="number" class="form-control precio-pieza-input" 
                               name="piezas[PINDEX][precio_unitario]" 
                               step="0.01" min="0" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Subtotal</label>
                        <input type="text" class="form-control subtotal-pieza" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block btn-sm btn-eliminar-pieza-trabajo">
                            <i class="fas fa-trash"></i> Quitar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

                <!-- Observaciones Generales -->
                <div class="form-group">
                    <label for="observaciones">Observaciones Generales</label>
                    <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                              id="observaciones" 
                              name="observaciones" 
                              rows="3"
                              placeholder="Observaciones generales del trabajo...">{{ old('observaciones') }}</textarea>
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
                    <i class="fas fa-save"></i> Registrar Trabajo
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

@section('js')
<script>
    $(document).ready(function() {
        let servicioIndex = 0;
        let piezaTrabajoIndex = 0;

        // ============ GESTIÓN DE SERVICIOS ============
        
        // Agregar nuevo servicio
        $('#btn-agregar-servicio').on('click', function() {
            let template = $('#servicio-template').html();
            template = template.replace(/INDEX/g, servicioIndex);
            $('#servicios-container').append(template);
            $('#alert-sin-servicios').hide();
            servicioIndex++;
        });

        // Eliminar servicio
        $(document).on('click', '.btn-eliminar-servicio', function() {
            $(this).closest('.servicio-item').remove();
            if ($('.servicio-item').length === 0) {
                $('#alert-sin-servicios').show();
                $('#btn-cargar-piezas-servicio').prop('disabled', true);
            }
            calcularTotales();
        });

        // Cuando cambia el servicio seleccionado, autocompletar importes
        $(document).on('change', '.servicio-select', function() {
            let selectedOption = $(this).find('option:selected');
            let costo = parseFloat(selectedOption.data('costo')) || 0;
            let comision = parseFloat(selectedOption.data('comision')) || 0;
            let card = $(this).closest('.servicio-item');
            let cantidad = parseFloat(card.find('.cantidad-input').val()) || 1;

            card.find('.importe-cliente-input').val((costo * cantidad).toFixed(2));
            card.find('.importe-tecnico-input').val((comision * cantidad).toFixed(2));
            
            calcularTotales();
            
            // Habilitar botón de cargar piezas si es el primer servicio
            if ($('.servicio-select').first().val()) {
                $('#btn-cargar-piezas-servicio').prop('disabled', false);
            }
        });

        // Cuando cambia la cantidad, recalcular importes
        $(document).on('input', '.cantidad-input', function() {
            let card = $(this).closest('.servicio-item');
            let selectedOption = card.find('.servicio-select option:selected');
            let costo = parseFloat(selectedOption.data('costo')) || 0;
            let comision = parseFloat(selectedOption.data('comision')) || 0;
            let cantidad = parseFloat($(this).val()) || 1;

            card.find('.importe-cliente-input').val((costo * cantidad).toFixed(2));
            card.find('.importe-tecnico-input').val((comision * cantidad).toFixed(2));
            
            calcularTotales();
        });

        // Cuando se cambian los importes manualmente
        $(document).on('input', '.importe-cliente-input, .importe-tecnico-input', function() {
            calcularTotales();
        });

        // Calcular totales generales
        function calcularTotales() {
            let totalCliente = 0;
            let totalTecnico = 0;

            $('.servicio-item').each(function() {
                let importeCliente = parseFloat($(this).find('.importe-cliente-input').val()) || 0;
                let importeTecnico = parseFloat($(this).find('.importe-tecnico-input').val()) || 0;
                totalCliente += importeCliente;
                totalTecnico += importeTecnico;
            });

            $('#total-cliente').text('Bs ' + totalCliente.toFixed(2));
            $('#total-tecnico').text('Bs ' + totalTecnico.toFixed(2));
        }

        // ============ GESTIÓN DE PIEZAS ============
        
        // Agregar pieza al trabajo 
        $('#btn-agregar-pieza-trabajo').on('click', function() {
            let template = $('#pieza-trabajo-template').html();
            template = template.replace(/PINDEX/g, piezaTrabajoIndex);
            $('#piezas-trabajo-container').append(template);
            $('#alert-sin-piezas-trabajo').hide();
            piezaTrabajoIndex++;
        });

        // Eliminar pieza del trabajo
        $(document).on('click', '.btn-eliminar-pieza-trabajo', function() {
            $(this).closest('.pieza-trabajo-item').remove();
            if ($('.pieza-trabajo-item').length === 0) {
                $('#alert-sin-piezas-trabajo').show();
            }
            calcularTotales();
        });

        // Cuando se selecciona una pieza, autocompletar precio y unidad
        $(document).on('change', '.pieza-trabajo-select', function() {
            let selectedOption = $(this).find('option:selected');
            let unidad = selectedOption.data('unidad');
            let precio = selectedOption.data('precio');
            let stock = selectedOption.data('stock');
            let tipo = selectedOption.data('tipo');
            let card = $(this).closest('.pieza-trabajo-item');
            
            card.find('.unidad-pieza').text(unidad + '(s)');
            card.find('.precio-pieza-input').val(precio);
            
            // Advertencia de stock bajo
            if (tipo === 'contable' && stock <= 0) {
                card.addClass('border-danger');
                if (card.find('.alerta-stock').length === 0) {
                    card.find('.form-group').first().append(
                        '<small class="text-danger d-block alerta-stock"><i class="fas fa-exclamation-triangle"></i> Sin stock disponible</small>'
                    );
                }
            } else {
                card.removeClass('border-danger');
                card.find('.alerta-stock').remove();
            }
            
            calcularSubtotalPieza(card);
        });

        // Calcular subtotal de pieza
        $(document).on('input', '.cantidad-pieza-input, .precio-pieza-input', function() {
            let card = $(this).closest('.pieza-trabajo-item');
            calcularSubtotalPieza(card);
        });

        function calcularSubtotalPieza(card) {
            let cantidad = parseFloat(card.find('.cantidad-pieza-input').val()) || 0;
            let precio = parseFloat(card.find('.precio-pieza-input').val()) || 0;
            let subtotal = cantidad * precio;
            card.find('.subtotal-pieza').val('Bs ' + subtotal.toFixed(2));
        }

        // Cargar piezas sugeridas del servicio seleccionado
        $('#btn-cargar-piezas-servicio').on('click', function() {
            // Obtener el primer servicio seleccionado
            let primerServicio = $('.servicio-select').first().val();
            
            if (!primerServicio) {
                alert('Primero seleccione un servicio.');
                return;
            }

            // Hacer petición AJAX para obtener piezas del servicio
            $.ajax({
                url: '/servicios/' + primerServicio + '/piezas',
                type: 'GET',
                success: function(response) {
                    if (response.piezas && response.piezas.length > 0) {
                        // Limpiar piezas actuales
                        $('#piezas-trabajo-container').empty();
                        piezaTrabajoIndex = 0;
                        
                        // Agregar cada pieza sugerida
                        response.piezas.forEach(function(pieza) {
                            let template = $('#pieza-trabajo-template').html();
                            template = template.replace(/PINDEX/g, piezaTrabajoIndex);
                            $('#piezas-trabajo-container').append(template);
                            
                            let card = $('.pieza-trabajo-item').last();
                            card.find('.pieza-trabajo-select').val(pieza.id_inventario).trigger('change');
                            card.find('.cantidad-pieza-input').val(pieza.cantidad_base);
                            
                            piezaTrabajoIndex++;
                        });
                        
                        $('#alert-sin-piezas-trabajo').hide();
                    } else {
                        alert('Este servicio no tiene piezas sugeridas asociadas.');
                    }
                },
                error: function() {
                    alert('Error al cargar las piezas del servicio.');
                }
            });
        });

        // Convertir placas a mayúsculas automáticamente
        $('#placas').on('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Validación antes de enviar el formulario
        $('#form-trabajo').on('submit', function(e) {
            if ($('.servicio-item').length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un servicio antes de registrar el trabajo.');
                return false;
            }
        });
    });
</script>
@stop

@section('css')
    @vite('resources/css/adminlte-theme.css')
@stop