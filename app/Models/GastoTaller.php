<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class GastoTaller extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'gastos_taller';
    protected $primaryKey = 'id_gasto';

    protected $fillable = [
        'fecha',
        'concepto',
        'descripcion',
        'monto',
        'comprobante',
        'id_empleado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_gasto';
    }

    // Relación: Un gasto pertenece a un empleado (quien lo registró)
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }
}