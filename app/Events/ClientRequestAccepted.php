<?php

namespace App\Events;

use App\Models\ClientRequest;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class ClientRequestAccepted implements ShouldBroadcast, ShouldQueue
{
    use Queueable, SerializesModels;

    public ClientRequest $clientRequest;

    public function __construct(ClientRequest $clientRequest)
    {
        $this->clientRequest = $clientRequest->load('driver');
    }

    public function broadcastOn()
    {
        return new PrivateChannel(
            'dashboard.cliente.'.$this->clientRequest->cliente_id
        );
    }

    public function broadcastAs(): string
    {
        return 'client-request.accepted';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->clientRequest->id,
            'status' => $this->clientRequest->status,
            'elapsed_minutes' => $this->clientRequest->minutosDesdeAceptacion(),
            'eta_minutes' => $this->clientRequest->minutosRestantes(),
            'driver' => $this->clientRequest->driver
                ? [
                    'id' => $this->clientRequest->driver->id,
                    'name' => $this->clientRequest->driver->name,
                ]
                : null,
        ];
    }
}
