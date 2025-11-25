<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Proveedor extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'proveedores';
    protected $primaryKey = 'id_proveedor';

    protected $fillable = [
        'nombre',
        'telefono',
        'direccion',
        'email',
    ];

    /**
     * Get the route key for the model.
     *
     */
    public function getRouteKeyName()
    {
        return 'id_proveedor';
    }

    // Relación: Un proveedor tiene muchos items de inventario
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_proveedor', 'id_proveedor');
    }
}