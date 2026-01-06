<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flotilla extends Model
{
    /**
     * Campos asignables
     */
    protected $fillable = [
        'cliente_id',
        'nombre',
        'activa',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    // 🔗 Flotilla pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // 🔗 Flotilla tiene muchos drivers
    public function drivers()
    {
        return $this->hasMany(User::class)
            ->where('rol', 'driver');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes útiles
    |--------------------------------------------------------------------------
    */

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}
