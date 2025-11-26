<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Servicio extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'servicios';
    protected $primaryKey = 'id_servicio';

    protected $fillable = [
        'nombre',
        'costo',
        'comision',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'comision' => 'decimal:2',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_servicio';
    }

    // Relación: Un servicio tiene muchos trabajos
    public function trabajos()
    {
        return $this->hasMany(Trabajo::class, 'id_servicio', 'id_servicio');
    }

    // Relación: Un servicio puede estar en muchos trabajos (a través de trabajo_servicios)
    public function trabajoServicios()
    {
        return $this->hasMany(TrabajoServicio::class, 'id_servicio', 'id_servicio');
    }

    // Relación: Un servicio tiene muchos items de inventario asociados
    public function servicioInventarios()
    {
        return $this->hasMany(ServicioInventario::class, 'id_servicio', 'id_servicio');
    }

    // Relación: Muchos a muchos con inventario
    public function inventarios()
    {
        return $this->belongsToMany(Inventario::class, 'servicio_inventario', 'id_servicio', 'id_inventario')
            ->withPivot('cantidad_base', 'es_opcional')
            ->withTimestamps();
    }
}