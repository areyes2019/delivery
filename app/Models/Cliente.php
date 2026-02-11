<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\UserRole;

class Cliente extends Model
{
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

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function admins()
    {
        return $this->hasMany(User::class)
            ->where('rol', UserRole::ADMIN_CLIENTE);
    }

    public function flotillas()
    {
        return $this->hasMany(Flotilla::class);
    }

    public function drivers()
    {
        return $this->hasMany(User::class)
            ->where('rol', UserRole::DRIVER);
    }

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
