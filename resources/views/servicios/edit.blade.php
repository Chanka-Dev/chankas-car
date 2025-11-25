@extends('adminlte::page')

@section('title', 'Editar Servicio - Chankas Car')

@section('content_header')
    <h1>Editar Servicio</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Edición</h3>
        </div>
        <form action="{{ route('servicios.update', $servicio->id_servicio) }}" method="POST" id="form-servicio">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="nombre">Nombre del Servicio</label>
                    <input type="text" 
                           class="form-control @error('nombre') is-invalid @enderror" 
                           id="nombre" 
                           name="nombre" 
                           value="{{ old('nombre', $servicio->nombre) }}"
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
                                   value="{{ old('costo', $servicio->costo) }}"
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
                                   value="{{ old('comision', $servicio->comision) }}"
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

                <!-- Piezas/Inventario Asociado -->
                <div class="card card-primary mt-3">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-cog"></i> Piezas/Inventario Típicamente Necesario</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" id="btn-agregar-pieza">
                                <i class="fas fa-plus"></i> Agregar Pieza
                            </button>
                            <small class="text-muted ml-2">Opcional: Defina qué piezas suele necesitar este servicio</small>
                        </div>

                        <div id="piezas-container">
                            <!-- Las piezas se agregarán aquí -->
                        </div>

                        <div class="alert alert-info" id="alert-sin-piezas">
                            <i class="fas fa-info-circle"></i> No hay piezas asociadas. Esto es opcional.
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('servicios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <!-- Template para nueva pieza -->
    <template id="pieza-template">
        <div class="card card-outline card-secondary pieza-item mb-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Pieza/Item</label>
                            <select class="form-control pieza-select" name="piezas[INDEX][id_inventario]" required>
                                <option value="">Seleccione una pieza...</option>
                                @foreach($inventarios as $item)
                                    <option value="{{ $item->id_inventario }}"
                                            data-unidad="{{ $item->unidad_medida }}"
                                            data-stock="{{ $item->stock_actual }}">
                                        {{ $item->nombre }} (Stock: {{ $item->stock_actual }} {{ $item->unidad_medida }}s)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Cantidad Típica</label>
                            <input type="number" class="form-control" name="piezas[INDEX][cantidad_base]" value="1" min="1" step="0.01" required>
                            <small class="text-muted unidad-medida">unidad(es)</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Opcional</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="piezas[INDEX][es_opcional]" value="1" id="opcional-INDEX">
                                <label class="custom-control-label" for="opcional-INDEX">
                                    Es opcional
                                </label>
                            </div>
                            <small class="text-muted">No siempre se usa</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-block btn-sm btn-eliminar-pieza">
                                <i class="fas fa-trash"></i> Quitar
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
        let piezaIndex = 0;

        // Agregar pieza
        $('#btn-agregar-pieza').on('click', function() {
            let template = $('#pieza-template').html();
            template = template.replace(/INDEX/g, piezaIndex);
            $('#piezas-container').append(template);
            $('#alert-sin-piezas').hide();
            piezaIndex++;
        });

        // Eliminar pieza
        $(document).on('click', '.btn-eliminar-pieza', function() {
            $(this).closest('.pieza-item').remove();
            if ($('.pieza-item').length === 0) {
                $('#alert-sin-piezas').show();
            }
        });

        // Actualizar unidad de medida cuando se selecciona una pieza
        $(document).on('change', '.pieza-select', function() {
            let unidad = $(this).find('option:selected').data('unidad');
            let stock = $(this).find('option:selected').data('stock');
            let card = $(this).closest('.pieza-item');
            
            card.find('.unidad-medida').text(unidad + '(s)');
            
            if (stock <= 0) {
                card.addClass('border-warning');
                card.find('.form-group').first().append('<small class="text-warning d-block"><i class="fas fa-exclamation-triangle"></i> Sin stock disponible</small>');
            }
        });

        // Cargar piezas existentes
        @foreach($servicio->servicioInventarios as $si)
            let template{{ $loop->index }} = $('#pieza-template').html();
            template{{ $loop->index }} = template{{ $loop->index }}.replace(/INDEX/g, piezaIndex);
            $('#piezas-container').append(template{{ $loop->index }});
            
            let card{{ $loop->index }} = $('.pieza-item').last();
            card{{ $loop->index }}.find('.pieza-select').val('{{ $si->id_inventario }}');
            card{{ $loop->index }}.find('input[name*="cantidad_base"]').val('{{ $si->cantidad_base }}');
            
            @if($si->es_opcional)
                card{{ $loop->index }}.find('input[name*="es_opcional"]').prop('checked', true);
            @endif
            
            let unidad{{ $loop->index }} = card{{ $loop->index }}.find('.pieza-select option:selected').data('unidad');
            card{{ $loop->index }}.find('.unidad-medida').text(unidad{{ $loop->index }} + '(s)');
            
            piezaIndex++;
        @endforeach

        if ($('.pieza-item').length > 0) {
            $('#alert-sin-piezas').hide();
        }
    });
</script>
@stop