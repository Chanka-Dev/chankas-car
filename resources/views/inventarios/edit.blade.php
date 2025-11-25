@extends('adminlte::page')

@section('title', 'Editar Item - Chankas Car')

@section('content_header')
    <h1>Editar Item de Inventario</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Edición</h3>
        </div>
        <form action="{{ route('inventarios.update', $inventario->id_inventario) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <!-- Información Básica -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre">Nombre del Item</label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="{{ old('nombre', $inventario->nombre) }}"
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
                            <label for="unidad_medida">Unidad de Medida</label>
                            <select class="form-control @error('unidad_medida') is-invalid @enderror" 
                                    id="unidad_medida" 
                                    name="unidad_medida" 
                                    required>
                                <option value="unidad" {{ old('unidad_medida', $inventario->unidad_medida) == 'unidad' ? 'selected' : '' }}>Unidad</option>
                                <option value="metro" {{ old('unidad_medida', $inventario->unidad_medida) == 'metro' ? 'selected' : '' }}>Metro</option>
                                <option value="kilo" {{ old('unidad_medida', $inventario->unidad_medida) == 'kilo' ? 'selected' : '' }}>Kilogramo</option>
                                <option value="litro" {{ old('unidad_medida', $inventario->unidad_medida) == 'litro' ? 'selected' : '' }}>Litro</option>
                                <option value="caja" {{ old('unidad_medida', $inventario->unidad_medida) == 'caja' ? 'selected' : '' }}>Caja</option>
                                <option value="par" {{ old('unidad_medida', $inventario->unidad_medida) == 'par' ? 'selected' : '' }}>Par</option>
                            </select>
                            @error('unidad_medida')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                              id="descripcion" 
                              name="descripcion" 
                              rows="3">{{ old('descripcion', $inventario->descripcion) }}</textarea>
                    @error('descripcion')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Stock y Tipo -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="stock_actual">Stock Actual</label>
                            <input type="number" 
                                   class="form-control @error('stock_actual') is-invalid @enderror" 
                                   id="stock_actual" 
                                   name="stock_actual" 
                                   min="0"
                                   value="{{ old('stock_actual', $inventario->stock_actual) }}"
                                   required>
                            @error('stock_actual')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="stock_minimo">Stock Mínimo</label>
                            <input type="number" 
                                   class="form-control @error('stock_minimo') is-invalid @enderror" 
                                   id="stock_minimo" 
                                   name="stock_minimo" 
                                   min="0"
                                   value="{{ old('stock_minimo', $inventario->stock_minimo) }}"
                                   required>
                            @error('stock_minimo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Alerta cuando el stock llegue a este nivel</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tipo_stock">Tipo de Stock</label>
                            <select class="form-control @error('tipo_stock') is-invalid @enderror" 
                                    id="tipo_stock" 
                                    name="tipo_stock" 
                                    required>
                                <option value="contable" {{ old('tipo_stock', $inventario->tipo_stock) == 'contable' ? 'selected' : '' }}>Contable (se descuenta automático)</option>
                                <option value="pregunta" {{ old('tipo_stock', $inventario->tipo_stock) == 'pregunta' ? 'selected' : '' }}>Pregunta (se pregunta si hay)</option>
                            </select>
                            @error('tipo_stock')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Precios -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="precio_compra">Precio de Compra (Bs)</label>
                            <input type="number" 
                                   class="form-control @error('precio_compra') is-invalid @enderror" 
                                   id="precio_compra" 
                                   name="precio_compra" 
                                   step="0.01"
                                   min="0"
                                   value="{{ old('precio_compra', $inventario->precio_compra) }}"
                                   required>
                            @error('precio_compra')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="precio_venta">Precio de Venta (Bs)</label>
                            <input type="number" 
                                   class="form-control @error('precio_venta') is-invalid @enderror" 
                                   id="precio_venta" 
                                   name="precio_venta" 
                                   step="0.01"
                                   min="0"
                                   value="{{ old('precio_venta', $inventario->precio_venta) }}"
                                   required>
                            @error('precio_venta')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Proveedor y Fecha -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_proveedor">Proveedor</label>
                            <select class="form-control @error('id_proveedor') is-invalid @enderror" 
                                    id="id_proveedor" 
                                    name="id_proveedor">
                                <option value="">Sin proveedor asignado</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id_proveedor }}" 
                                        {{ old('id_proveedor', $inventario->id_proveedor) == $proveedor->id_proveedor ? 'selected' : '' }}>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_proveedor')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_ingreso">Fecha de Ingreso</label>
                            <input type="date" 
                                   class="form-control @error('fecha_ingreso') is-invalid @enderror" 
                                   id="fecha_ingreso" 
                                   name="fecha_ingreso" 
                                   value="{{ old('fecha_ingreso', $inventario->fecha_ingreso?->format('Y-m-d')) }}">
                            @error('fecha_ingreso')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                @if($inventario->stock_actual <= $inventario->stock_minimo)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Advertencia:</strong> Este item tiene stock bajo. Stock actual: {{ $inventario->stock_actual }} {{ $inventario->unidad_medida }}(s).
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('inventarios.index') }}" class="btn btn-secondary">
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
    // Calcular margen de ganancia automáticamente
    $('#precio_compra, #precio_venta').on('input', function() {
        let compra = parseFloat($('#precio_compra').val()) || 0;
        let venta = parseFloat($('#precio_venta').val()) || 0;
        
        if (compra > 0 && venta > 0) {
            let margen = ((venta - compra) / compra * 100).toFixed(2);
            let color = margen > 0 ? 'success' : 'danger';
            
            $('#margen-info').remove();
            $('#precio_venta').parent().append(
                `<small id="margen-info" class="form-text text-${color}">Margen de ganancia: ${margen}%</small>`
            );
        }
    });
</script>
@stop