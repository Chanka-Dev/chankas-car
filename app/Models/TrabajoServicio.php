<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrabajoServicio extends Model
{
    use HasFactory;

    protected $table = 'trabajo_servicios';

    protected $fillable = [
        'id_trabajo',
        'id_servicio',
        'cantidad',
        'importe_cliente',
        'importe_tecnico',
        'observaciones',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'importe_cliente' => 'decimal:2',
        'importe_tecnico' => 'decimal:2',
    ];

    // Relación: Un trabajo_servicio pertenece a un trabajo
    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class, 'id_trabajo', 'id_trabajo');
    }

    // Relación: Un trabajo_servicio pertenece a un servicio
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }
}