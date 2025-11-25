<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Inventario extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'inventario';
    protected $primaryKey = 'id_inventario';

    protected $fillable = [
        'nombre',
        'descripcion',
        'unidad_medida',
        'precio_compra',
        'precio_venta',
        'cantidad', // stock antiguo
        'stock_actual',
        'stock_minimo',
        'tipo_stock',
        'id_proveedor',
        'fecha_ingreso',
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'cantidad' => 'integer',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'fecha_ingreso' => 'date',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_inventario';
    }

    // Relación: Un item de inventario pertenece a un proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    // Relación: Un item puede estar en muchos servicios
    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'servicio_inventario', 'id_inventario', 'id_servicio')
            ->withPivot('cantidad_base', 'es_opcional')
            ->withTimestamps();
    }

    // Relación: Un item puede estar en muchos trabajos
    public function trabajos()
    {
        return $this->belongsToMany(Trabajo::class, 'trabajo_inventario', 'id_inventario', 'id_trabajo')
            ->withPivot('cantidad_usada', 'precio_unitario')
            ->withTimestamps();
    }
}