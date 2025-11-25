<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    /**
     * Relación: Un log pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el modelo afectado (polimórfico manual)
     */
    public function getModelAttribute()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * Registrar una actividad
     */
    public static function log($action, $description, $modelType = null, $modelId = null, $changes = null)
    {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Obtener el ícono según la acción
     */
    public function getIconAttribute()
    {
        $icons = [
            'created' => 'fas fa-plus-circle text-success',
            'updated' => 'fas fa-edit text-primary',
            'deleted' => 'fas fa-trash text-danger',
            'viewed' => 'fas fa-eye text-info',
            'login' => 'fas fa-sign-in-alt text-success',
            'logout' => 'fas fa-sign-out-alt text-secondary',
        ];

        return $icons[$this->action] ?? 'fas fa-circle text-muted';
    }
}
