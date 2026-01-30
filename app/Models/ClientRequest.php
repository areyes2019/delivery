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

    protected $casts = [
        'pickup_position'      => 'array',
        'destination_position' => 'array',
        'accepted_at'          => 'datetime',
        'started_at'           => 'datetime',
        'paid_at'              => 'datetime',
        'delivered_at'         => 'datetime',
    ];

    protected $fillable = [
        'cliente_id',
        'flotilla_id',
        'created_by_dispatcher_id',
        'driver_id',
        'status',

        'remitente_nombre',
        'remitente_telefono',
        'destinatario_nombre',
        'destinatario_telefono',

        'pickup_position',
        'destination_position',
        'pickup_description',
        'destination_description',

        'fare_offered',
        'product_amount',
        'total_expected',

        'accepted_at',
        'started_at',
        'paid_at',
        'delivered_at',

        'observaciones',
    ];

    /* ===================== SCOPES ===================== */

    public function scopeDeCliente($query, int $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopeEnProceso($query)
    {
        return $query->whereIn('status', [
            EntregaStatus::ACCEPTED->value,
            EntregaStatus::PICKED_UP->value,
            EntregaStatus::PAID->value,
        ]);
    }

    /* ===================== RELACIONES ===================== */

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function flotilla()
    {
        return $this->belongsTo(Flotilla::class);
    }

    public function entregaPago()
    {
        return $this->hasOne(EntregaPago::class);
    }

    /* ===================== ESTADOS ===================== */

    public function marcarComoAceptada(User $driver): void
    {
        $this->ensure(
            $this->status === EntregaStatus::CREATED->value,
            'La solicitud no puede ser aceptada'
        );

        $this->update([
            'status'      => EntregaStatus::ACCEPTED->value,
            'driver_id'   => $driver->id,
            'flotilla_id' => $driver->flotilla_id,
            'accepted_at' => now(),
        ]);
    }

    public function iniciarEntrega(User $driver): void
    {
        $this->ensure(
            $this->status === EntregaStatus::ACCEPTED->value,
            'La entrega no puede iniciar'
        );

        $this->ensure(
            $this->driver_id === $driver->id,
            'No eres el driver asignado'
        );

        $this->update([
            'status'     => EntregaStatus::PICKED_UP->value,
            'started_at' => now(),
        ]);
    }

    public function marcarComoPagada(): void
    {
        $this->ensure(
            $this->status === EntregaStatus::PICKED_UP->value,
            'La entrega no puede marcarse como pagada'
        );

        $this->update([
            'status'  => EntregaStatus::PAID->value,
            'paid_at' => now(),
        ]);
    }

    public function marcarComoEntregada(): void
    {
        $this->ensure(
            in_array($this->status, [
                EntregaStatus::PICKED_UP->value,
                EntregaStatus::PAID->value,
            ], true),
            'La entrega no puede finalizarse'
        );

        $this->update([
            'status'       => EntregaStatus::DELIVERED->value,
            'delivered_at' => now(),
        ]);
    }

    /* ===================== ETA / TIEMPOS ===================== */

    /**
     * Minutos transcurridos desde que fue aceptada
     */
    public function minutosDesdeAceptacion(): ?int
    {
        if (! $this->accepted_at instanceof Carbon) {
            return null;
        }

        return $this->accepted_at->diffInMinutes(now());
    }

    /**
     * Minutos restantes estimados (ETA simple)
     * NO rompe aunque no tengas ETA real todavía
     */
    public function minutosRestantes(int $etaBase = 30): ?int
    {
        if (! $this->accepted_at instanceof Carbon) {
            return null;
        }

        $transcurridos = $this->accepted_at->diffInMinutes(now());

        return max($etaBase - $transcurridos, 0);
    }

    /* ===================== HELPERS ===================== */

    private function ensure(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new \LogicException($message);
        }
    }
}
