<?php

namespace App\Services;

use App\Models\Entrega;
use App\Models\User;
use App\Enums\EntregaEstado;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EntregaService
{
    /**
     * 🆕 Crear entrega
     */
    public function create(array $data, User $user): Entrega
    {
        return Entrega::create([
            'cliente_id'  => $user->cliente_id,
            'flotilla_id' => $data['flotilla_id'],
            'driver_id'   => null,

            'remitente_nombre'   => $data['remitente_nombre'],
            'remitente_telefono' => $data['remitente_telefono'],

            'destinatario_nombre'   => $data['destinatario_nombre'],
            'destinatario_telefono' => $data['destinatario_telefono'],

            'pickup_position'      => $data['pickup_position'],
            'destination_position' => $data['destination_position'],

            'pickup_description'      => $data['pickup_description'] ?? null,
            'destination_description' => $data['destination_description'] ?? null,

            'estado'        => EntregaEstado::CREADA,
            'observaciones' => $data['observaciones'] ?? null,
        ]);
    }

    /**
     * 🚗 Driver acepta entrega
     */
    public function acceptEntrega(Entrega $entrega, User $driver): Entrega
    {
        if ($entrega->estado !== EntregaEstado::CREADA) {
            throw new HttpException(422, 'La entrega ya no está disponible');
        }

        if ($entrega->flotilla_id !== $driver->flotilla_id) {
            throw new HttpException(403, 'No perteneces a esta flotilla');
        }

        $entregaActiva = Entrega::where('driver_id', $driver->id)
            ->whereIn('estado', [EntregaEstado::ASIGNADA, EntregaEstado::EN_CAMINO])
            ->exists();

        if ($entregaActiva) {
            throw new HttpException(422, 'Ya tienes una entrega activa');
        }

        $entrega->update([
            'driver_id' => $driver->id,
            'estado'    => EntregaEstado::ASIGNADA,
        ]);

        return $entrega;
    }

    /**
     * 🔄 Cambio de estado controlado
     */
    public function changeEstado(
        int $entregaId,
        EntregaEstado $nuevoEstado,
        User $driver
    ): Entrega {

        $entrega = Entrega::with('clientRequest.entregaPago')->findOrFail($entregaId);

        if ($entrega->driver_id !== $driver->id) {
            throw new HttpException(403, 'No puedes modificar esta entrega');
        }

        $flujoValido = [
            EntregaEstado::ASIGNADA->value  => EntregaEstado::EN_CAMINO->value,
            EntregaEstado::EN_CAMINO->value => EntregaEstado::ENTREGADA->value,
        ];

        if (
            !isset($flujoValido[$entrega->estado->value]) ||
            $flujoValido[$entrega->estado->value] !== $nuevoEstado->value
        ) {
            throw new HttpException(422, 'Transición de estado inválida');
        }

        if (
            $nuevoEstado === EntregaEstado::EN_CAMINO &&
            $entrega->clientRequest->payment_timing === 'PREPAID' &&
            !$entrega->clientRequest->entregaPago
        ) {
            throw new HttpException(422, 'Debes cobrar antes de iniciar el viaje');
        }

        if (
            $nuevoEstado === EntregaEstado::ENTREGADA &&
            $entrega->clientRequest->payment_timing === 'CASH_ON_DELIVERY'
        ) {
            $this->validatePagoParaEntrega($entrega);
            $entrega->delivered_at = now();
        }

        $entrega->estado = $nuevoEstado;
        $entrega->save();

        return $entrega;
    }

    /**
     * ❌ Cancelar entrega (ANTES de EN_CAMINO)
     */
    public function cancelar(int $entregaId, User $user, ?string $motivo = null): Entrega
    {
        return DB::transaction(function () use ($entregaId, $user, $motivo) {

            $entrega = Entrega::lockForUpdate()->findOrFail($entregaId);

            if (in_array($entrega->estado, [
                EntregaEstado::EN_CAMINO,
                EntregaEstado::ENTREGADA,
            ])) {
                throw new HttpException(409, 'La entrega ya no puede ser cancelada');
            }

            $esAdminCliente =
                $user->rol === 'admin_cliente' &&
                $user->cliente_id === $entrega->cliente_id;

            $esDriver =
                $user->rol === 'driver' &&
                $user->id === $entrega->driver_id;

            if (! $esAdminCliente && ! $esDriver) {
                throw new HttpException(403, 'No autorizado para cancelar esta entrega');
            }

            $entrega->update([
                'estado'         => EntregaEstado::CANCELADA,
                'cancelled_by'   => $user->id,
                'cancelled_role' => $user->rol,
                'cancelled_at'   => now(),
                'cancel_reason'  => $motivo,
            ]);

            return $entrega;
        });
    }

    /**
     * 🔐 Validar pago
     */
    private function validatePagoParaEntrega(Entrega $entrega): void
    {
        $pago = $entrega->clientRequest?->entregaPago;

        if (!$pago || $pago->status !== 'CONFIRMED') {
            throw new HttpException(422, 'Pago inválido o no confirmado');
        }
    }
}
