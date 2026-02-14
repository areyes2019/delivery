<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\UserRole;

class Flotilla extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'cliente_id',
        'nombre',
        'activa',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'activa' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * 🔗 Flotilla pertenece a un cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * 🔗 Flotilla tiene muchos drivers
     */
    public function drivers()
    {
        return $this->hasMany(User::class)
            ->where('rol', UserRole::DRIVER);
    }

    /**
     * 🔗 Flotilla tiene un solo despachador
     */
    public function despachador()
    {
        return $this->hasOne(User::class)
            ->where('rol', UserRole::DESPACHADOR);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: solo flotillas activas
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activa', true);
    }
}
