<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'id_empleado',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Relación: Un usuario puede estar vinculado a un empleado
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }

    /**
     * Relación: Un usuario tiene muchos logs de actividad
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Verificar si el usuario es admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar si el usuario es técnico
     */
    public function isTecnico()
    {
        return $this->role === 'tecnico';
    }

    /**
     * Verificar si el usuario es cajero
     */
    public function isCajero()
    {
        return $this->role === 'cajero';
    }

    /**
     * Verificar si el usuario tiene al menos uno de los roles especificados
     */
    public function hasRole(...$roles)
    {
        return in_array($this->role, $roles);
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Obtener el nombre del rol en español
     */
    public function getRoleNameAttribute()
    {
        $roles = [
            'admin' => 'Administrador',
            'tecnico' => 'Técnico',
            'cajero' => 'Cajero',
            'lectura' => 'Solo Lectura',
        ];

        return $roles[$this->role] ?? 'Desconocido';
    }
}
