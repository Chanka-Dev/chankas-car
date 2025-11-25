<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Trabajo extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'trabajos';
    protected $primaryKey = 'id_trabajo';

    protected $fillable = [
        'fecha_trabajo',
        'fecha_recepcion',
        'fecha_recalificacion',
        'id_empleado',
        'id_cliente',
        'observaciones',
    ];

    protected $casts = [
        'fecha_trabajo' => 'date',
        'fecha_recepcion' => 'date',
        'fecha_recalificacion' => 'date',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_trabajo';
    }

    // Relación: Un trabajo pertenece a un empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }

    // Relación: Un trabajo pertenece a un cliente (puede ser null)
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    // Relación: Un trabajo tiene muchos servicios (a través de trabajo_servicios)
    public function trabajoServicios()
    {
        return $this->hasMany(TrabajoServicio::class, 'id_trabajo', 'id_trabajo');
    }

    // Relación: Muchos a muchos con servicios
    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'trabajo_servicios', 'id_trabajo', 'id_servicio')
            ->withPivot('cantidad', 'importe_cliente', 'importe_tecnico', 'observaciones')
            ->withTimestamps();
    }

    // Accessor: Calcular total cobrado al cliente
    public function getTotalClienteAttribute()
    {
        return $this->trabajoServicios->sum('importe_cliente');
    }

    // Accessor: Calcular total de comisiones
    public function getTotalTecnicoAttribute()
    {
        return $this->trabajoServicios->sum('importe_tecnico');
    }

    // Relación: Un trabajo tiene muchos items de inventario usados
    public function trabajoInventarios()
    {
        return $this->hasMany(TrabajoInventario::class, 'id_trabajo', 'id_trabajo');
    }

    // Relación: Muchos a muchos con inventario
    public function inventarios()
    {
        return $this->belongsToMany(Inventario::class, 'trabajo_inventario', 'id_trabajo', 'id_inventario')
            ->withPivot('cantidad_usada', 'precio_unitario')
            ->withTimestamps();
}
}