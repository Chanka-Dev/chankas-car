<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class PagoTecnico extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'pagos_tecnicos';
    protected $primaryKey = 'id_pago';

    protected $fillable = [
        'id_empleado',
        'fecha_pago',
        'monto_pagado',
        'periodo_inicio',
        'periodo_fin',
        'observaciones',
        'tipo_pago',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'periodo_inicio' => 'date',
        'periodo_fin' => 'date',
        'monto_pagado' => 'decimal:2',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_pago';
    }

    // Relación: Un pago pertenece a un empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }
}