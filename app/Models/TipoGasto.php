<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class TipoGasto extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'tipos_gastos';
    protected $primaryKey = 'id_tipo_gasto';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_tipo_gasto';
    }

    /**
     * Scope para obtener solo tipos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Verificar si el tipo de gasto está siendo usado
     */
    public function tieneGastos()
    {
        return GastoTaller::where('concepto', $this->nombre)->exists();
    }
}
