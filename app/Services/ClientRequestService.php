<?php

namespace App\Services;

use App\Models\ClientRequest;
use App\Models\User;

class ClientRequestService
{
    public function create(array $data, User $user): int
    {
        $clientRequest = ClientRequest::create([
            'cliente_id' => $user->cliente_id,

            // REMITENTE
            'remitente_nombre' => $data['remitente_nombre'],
            'remitente_telefono' => $data['remitente_telefono'] ?? null,

            // DESTINATARIO
            'destinatario_nombre' => $data['destinatario_nombre'],
            'destinatario_telefono' => $data['destinatario_telefono'] ?? null,

            // UBICACIONES
            'pickup_position' => $data['pickup_position'] ?? null,
            'destination_position' => $data['destination_position'] ?? null,

            'pickup_description' => $data['pickup_description'] ?? null,
            'destination_description' => $data['destination_description'],

            // COSTOS
            'fare_offered' => $data['fare_offered'] ?? 0,
            'product_amount' => $data['product_amount'] ?? 0,
            'total_expected' =>
                ($data['fare_offered'] ?? 0) +
                ($data['product_amount'] ?? 0),

            // CONTROL
            'status' => 'CREATED',
            'created_by_dispatcher_id' => $user->id,

            // EXTRA
            'observaciones' => $data['observaciones'] ?? null,
        ]);

        return $clientRequest->id;
    }
}

