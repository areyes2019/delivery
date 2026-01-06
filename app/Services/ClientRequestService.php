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

            'remitente_nombre' => $data['remitente_nombre'],
            'remitente_telefono' => $data['remitente_telefono'],

            'destinatario_nombre' => $data['destinatario_nombre'],
            'destinatario_telefono' => $data['destinatario_telefono'],

            'pickup_position' => json_encode($data['pickup_position']),
            'destination_position' => json_encode($data['destination_position']),

            'pickup_description' => $data['pickup_description'] ?? null,
            'destination_description' => $data['destination_description'] ?? null,

            'fare_offered' => $data['fare_offered'] ?? 0,
            'product_amount' => $data['product_amount'] ?? 0,

            'total_expected' =>
                ($data['fare_offered'] ?? 0) +
                ($data['product_amount'] ?? 0),

            'status' => 'CREATED',

            'created_by_dispatcher_id' => $user->id,

            'observaciones' => $data['observaciones'] ?? null,
        ]);

        return $clientRequest->id;
    }
}
