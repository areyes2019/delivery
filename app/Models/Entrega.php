<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\EntregaEstado;

class Entrega extends Model
{
    protected $table = 'entregas';

    protected $fillable = [
        'client_request_id',
        'cliente_id',
        'flotilla_id',
        'driver_id',

        // 👤 Remitente
        'remitente_nombre',
        'remitente_telefono',

        // 👤 Destinatario
        'destinatario_nombre',
        'destinatario_telefono',

        // 📍 Direcciones (ESTO FALTABA AQUÍ)
        'origen_direccion',
        'destino_direccion',

        // 📍 Posiciones
        'pickup_position',
        'destination_position',

        // 🚚 Estado
        'estado',

        'observaciones',
    ];


    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        // Enum nativo
        'estado' => EntregaEstado::class,

        // JSON → array
        'pickup_position' => 'array',
        'destination_position' => 'array',
        'delivered_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    // 🚗 Driver asignado
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // 📦 Solicitud origen
    public function clientRequest()
    {
        return $this->belongsTo(ClientRequest::class, 'client_request_id');
    }

    // 💰 Pago de la entrega
    public function pago()
    {
        return $this->hasOne(EntregaPago::class, 'entrega_id');
    }
}
