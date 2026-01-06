<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    /**
     * Campos asignables
     */
    protected $fillable = [
        'nombre',
        'slug',
        'email_contacto',
        'telefono',
        'plan',
        'activo',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    // 🔗 Cliente → Usuarios (admins, despachadores, drivers)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // 🔗 Cliente → Solo admins del cliente
    public function admins()
    {
        return $this->hasMany(User::class)
            ->where('rol', 'admin_cliente');
    }

    // 🔗 Cliente → Flotillas
    public function flotillas()
    {
        return $this->hasMany(Flotilla::class);
    }

    // 🔗 Cliente → Drivers (a través de users)
    public function drivers()
    {
        return $this->hasMany(User::class)
            ->where('rol', 'driver');
    }

    // 🔗 Cliente → Entregas / solicitudes
    public function entregas()
    {
        return $this->hasMany(ClientRequest::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
