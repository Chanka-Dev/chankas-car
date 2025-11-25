<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioInventario extends Model
{
    use HasFactory;

    protected $table = 'servicio_inventario';

    protected $fillable = [
        'id_servicio',
        'id_inventario',
        'cantidad_base',
        'es_opcional',
    ];

    protected $casts = [
        'cantidad_base' => 'integer',
        'es_opcional' => 'boolean',
    ];

    // Relación: Pertenece a un servicio
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }

    // Relación: Pertenece a un item de inventario
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'id_inventario', 'id_inventario');
    }
}