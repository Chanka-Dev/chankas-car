<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Empleado extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'empleados';
    protected $primaryKey = 'id_empleado';

    protected $fillable = [
        'ci',
        'nombre',
        'apellido',
        'telefono',
        'id_cargo',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_empleado';
    }

    // Relación: Un empleado pertenece a un cargo
    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'id_cargo', 'id_cargo');
    }

    // Relación: Un empleado tiene muchos trabajos
    public function trabajos()
    {
        return $this->hasMany(Trabajo::class, 'id_empleado', 'id_empleado');
    }
}