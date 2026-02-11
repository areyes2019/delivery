<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserRole;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'cliente_id',
        'flotilla_id',
        'parent_id',
        'name',
        'lastname',
        'email',
        'phone',
        'password',
        'rol',
        'activo',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden
    |--------------------------------------------------------------------------
    */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'activo' => 'boolean',
        'rol'    => UserRole::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function flotilla()
    {
        return $this->belongsTo(Flotilla::class);
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'driver_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de Rol
    |--------------------------------------------------------------------------
    */

    public function hasRole(UserRole $role): bool
    {
        return $this->rol === $role;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(UserRole::SUPER_ADMIN);
    }

    public function isAdminCliente(): bool
    {
        return $this->hasRole(UserRole::ADMIN_CLIENTE);
    }

    public function isDespachador(): bool
    {
        return $this->hasRole(UserRole::DESPACHADOR);
    }

    public function isDriver(): bool
    {
        return $this->hasRole(UserRole::DRIVER);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDrivers($query)
    {
        return $query->where('rol', UserRole::DRIVER);
    }

    public function scopeDespachadores($query)
    {
        return $query->where('rol', UserRole::DESPACHADOR);
    }

    public function scopeAdminsCliente($query)
    {
        return $query->where('rol', UserRole::ADMIN_CLIENTE);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
