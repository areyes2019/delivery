<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\Cliente;
use App\Models\Flotilla;
use App\Models\User;
use App\Models\EntregaPago;
use App\Enums\EntregaStatus;


class ClientRequest extends Model
{
    protected $table = 'client_requests';

    /*
    |------------------------------------------------------------------
    | Casts
    |------------------------------------------------------------------
    */
    protected $casts = [
        'pickup_position'      => 'array',
        'destination_position' => 'array',
        'started_at'           => 'datetime',
        'paid_at'              => 'datetime',
        'delivered_at'         => 'datetime',
    ];

    /*
    |------------------------------------------------------------------
    | Campos asignables
    |------------------------------------------------------------------
    */
    protected $fillable = [
        // 🔐 TENANT
        'cliente_id',
        'flotilla_id',

        // 👤 CONTROL
        'created_by_dispatcher_id',
        'driver_id',
        'status',

        // 📞 REMITENTE
        'remitente_nombre',
        'remitente_telefono',

        // 📦 DESTINATARIO
        'destinatario_nombre',
        'destinatario_telefono',

        // 📍 UBICACIONES
        'pickup_position',
        'destination_position',
        'pickup_description',
        'destination_description',

        // 💰 COSTOS
        'fare_offered',
        'product_amount',
        'total_expected',

        // ⏱️ TIMESTAMPS
        'started_at',
        'paid_at',
        'delivered_at',

        // 📝 EXTRA
        'observaciones',
    ];

    /* -----------------------------------------------------------------
     | Scopes
     |-----------------------------------------------------------------*/

    public function scopeDeCliente($query, int $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopeDisponibles($query)
    {
        return $query->where('status', EntregaStatus::CREATED->value);
    }

    /* -----------------------------------------------------------------
     | Relaciones
     |-----------------------------------------------------------------*/

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function flotilla()
    {
        return $this->belongsTo(Flotilla::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function createdByDispatcher()
    {
        return $this->belongsTo(User::class, 'created_by_dispatcher_id');
    }

    public function entregaPago()
    {
        return $this->hasOne(EntregaPago::class);
    }

    /* -----------------------------------------------------------------
     | Máquina de estados (FUENTE DE VERDAD)
     |-----------------------------------------------------------------*/

    /* ---------- CREATED → ACCEPTED ---------- */

    public function puedeSerAceptada(): bool
    {
        return $this->status === EntregaStatus::CREATED->value;
    }

    public function marcarComoAceptada(User $driver): void
    {
        if (! $this->puedeSerAceptada()) {
            throw new \LogicException('La solicitud no puede ser aceptada');
        }

        $this->update([
            'status'      => EntregaStatus::ACCEPTED->value,
            'driver_id'   => $driver->id,
            'flotilla_id' => $driver->flotilla_id,
        ]);
    }

    /* ---------- ACCEPTED → PICKED_UP ---------- */

    public function puedeIniciar(): bool
    {
        return $this->status === EntregaStatus::ACCEPTED->value;
    }

    public function iniciarEntrega(User $driver): void
    {
        if (! $this->puedeIniciar()) {
            throw new \LogicException('La solicitud no puede iniciar');
        }

        if ($this->driver_id !== $driver->id) {
            throw new \LogicException('No eres el driver asignado');
        }

        $this->update([
            'status'     => EntregaStatus::PICKED_UP->value,
            'started_at' => Carbon::now(),
        ]);
    }

    /* ---------- PICKED_UP → PAID ---------- */

    public function puedeMarcarPagada(): bool
    {
        return $this->status === EntregaStatus::PICKED_UP->value;
    }

    public function marcarComoPagada(): void
    {
        if (! $this->puedeMarcarPagada()) {
            throw new \LogicException('La entrega no puede marcarse como pagada');
        }

        $this->update([
            'status'  => EntregaStatus::PAID->value,
            'paid_at' => Carbon::now(),
        ]);
    }

    /* ---------- PAID / PICKED_UP → DELIVERED ---------- */

    public function puedeFinalizar(): bool
    {
        return in_array($this->status, [
            EntregaStatus::PICKED_UP->value,
            EntregaStatus::PAID->value,
        ], true);
    }

    public function marcarComoEntregada(): void
    {
        if (! $this->puedeFinalizar()) {
            throw new \LogicException('La entrega no puede finalizarse');
        }

        $this->update([
            'status'       => EntregaStatus::DELIVERED->value,
            'delivered_at' => Carbon::now(),
        ]);
    }

    /* ---------- CANCELACIÓN ---------- */

    public function cancelar(): void
    {
        if (in_array($this->status, [
            EntregaStatus::PICKED_UP->value,
            EntregaStatus::PAID->value,
            EntregaStatus::DELIVERED->value,
        ], true)) {
            throw new \LogicException('No se puede cancelar una entrega en curso o finalizada');
        }

        $this->update([
            'status' => EntregaStatus::CANCELED->value,
        ]);
    }
}
