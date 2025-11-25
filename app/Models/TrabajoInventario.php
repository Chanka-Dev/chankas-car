<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrabajoInventario extends Model
{
    use HasFactory;

    protected $table = 'trabajo_inventario';

    protected $fillable = [
        'id_trabajo',
        'id_inventario',
        'cantidad_usada',
        'precio_unitario',
    ];

    protected $casts = [
        'cantidad_usada' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
    ];

    // Relación: Pertenece a un trabajo
    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class, 'id_trabajo', 'id_trabajo');
    }

    // Relación: Pertenece a un item de inventario
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'id_inventario', 'id_inventario');
    }
}