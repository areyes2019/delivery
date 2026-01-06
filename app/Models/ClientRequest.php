<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\Cliente;
use App\Models\Flotilla;
use App\Models\User;
use App\Models\EntregaPago;

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
    | Campos asignables (alineados 1:1 con la BD)
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

        // ⏱️ TIMESTAMPS DE FLUJO
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
        return $query->where('status', 'CREATED');
    }

    /* -----------------------------------------------------------------
     | Relaciones
     |-----------------------------------------------------------------*/

    // 🏢 Cliente (tenant)
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // 🚚 Flotilla asignada
    public function flotilla()
    {
        return $this->belongsTo(Flotilla::class);
    }

    // 👨‍✈️ Driver asignado
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // 👤 Despachador creador
    public function createdByDispatcher()
    {
        return $this->belongsTo(User::class, 'created_by_dispatcher_id');
    }

    // 💳 Pago asociado (si existe)
    public function entregaPago()
    {
        return $this->hasOne(EntregaPago::class);
    }

    /* -----------------------------------------------------------------
     | Lógica de dominio — Máquina de estados
     |-----------------------------------------------------------------*/

    /* ---------- ACCEPTED ---------- */

    public function puedeSerAceptada(): bool
    {
        return $this->status === 'CREATED';
    }

    public function marcarComoAceptada(User $driver): void
    {
        if (! $this->puedeSerAceptada()) {
            throw new \LogicException('La solicitud no puede ser aceptada');
        }

        $this->update([
            'status'      => 'ACCEPTED',
            'driver_id'   => $driver->id,
            'flotilla_id' => $driver->flotilla_id,
        ]);
    }

    /* ---------- EN_CAMINO ---------- */

    public function puedeIniciar(): bool
    {
        return $this->status === 'ACCEPTED';
    }

    public function iniciarEntrega(User $driver): void
    {
        if (! $this->puedeIniciar()) {
            throw new \LogicException('La solicitud no puede iniciar el viaje');
        }

        if ($this->driver_id !== $driver->id) {
            throw new \LogicException('No eres el driver asignado');
        }

        $this->update([
            'status'     => 'EN_CAMINO',
            'started_at' => Carbon::now(),
        ]);
    }

    /* ---------- PAGADA ---------- */

    public function puedeMarcarPagada(): bool
    {
        return $this->status === 'EN_CAMINO';
    }

    public function marcarComoPagada(): void
    {
        if (! $this->puedeMarcarPagada()) {
            throw new \LogicException('La entrega no puede marcarse como pagada');
        }

        $this->update([
            'status'  => 'PAGADA',
            'paid_at' => Carbon::now(),
        ]);
    }

    /* ---------- ENTREGADA ---------- */

    public function puedeFinalizar(): bool
    {
        return in_array($this->status, ['EN_CAMINO', 'PAGADA'], true);
    }

    public function marcarComoEntregada(): void
    {
        if (! $this->puedeFinalizar()) {
            throw new \LogicException('La entrega no puede finalizarse');
        }

        $this->update([
            'status'       => 'ENTREGADA',
            'delivered_at' => Carbon::now(),
        ]);
    }

    /* ---------- CANCELACIÓN ---------- */

    public function cancelar(): void
    {
        if (in_array($this->status, ['EN_CAMINO', 'PAGADA', 'ENTREGADA'], true)) {
            throw new \LogicException('No se puede cancelar una entrega en curso o finalizada');
        }

        $this->update([
            'status' => 'CANCELLED',
        ]);
    }
}
