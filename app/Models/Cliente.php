<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Cliente extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'placas',
        'telefono',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_cliente';
    }

    // Relación: Un cliente tiene muchos trabajos
    public function trabajos()
    {
        return $this->hasMany(Trabajo::class, 'id_cliente', 'id_cliente');
    }
}