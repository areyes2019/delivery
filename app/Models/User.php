<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'cliente_id',   // null solo para super_admin
        'flotilla_id',  // solo para driver
        'parent_id',    // jerarquía (quién lo creó / supervisa)
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
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones base
    |--------------------------------------------------------------------------
    */

    /**
     * Usuario pertenece a un cliente
     * (admin_cliente, despachador, driver)
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Solo drivers pertenecen a una flotilla
     */
    public function flotilla()
    {
        return $this->belongsTo(Flotilla::class);
    }

    /**
     * Driver → Entregas ejecutadas
     */
    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'driver_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Jerarquía de usuarios (auto-relación)
    |--------------------------------------------------------------------------
    */

    /**
     * Usuario que lo creó o supervisa
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Usuarios creados o supervisados por este usuario
     */
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de rol (REGLAS DE NEGOCIO)
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        return $this->rol === 'super_admin';
    }

    public function isAdminCliente(): bool
    {
        return $this->rol === 'admin_cliente';
    }

    public function isDespachador(): bool
    {
        return $this->rol === 'despachador';
    }

    public function isDriver(): bool
    {
        return $this->rol === 'driver';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes útiles
    |--------------------------------------------------------------------------
    */

    public function scopeDrivers($query)
    {
        return $query->where('rol', 'driver');
    }

    public function scopeDespachadores($query)
    {
        return $query->where('rol', 'despachador');
    }

    public function scopeAdminsCliente($query)
    {
        return $query->where('rol', 'admin_cliente');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
