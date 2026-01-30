<?php

namespace App\Events;

use App\Models\ClientRequest;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class ClientRequestPaid implements ShouldBroadcast, ShouldQueue
{
    use Queueable, SerializesModels;

    public ClientRequest $entrega;

    public function __construct(ClientRequest $entrega)
    {
        $this->entrega = $entrega;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('dashboard');
    }

    public function broadcastAs(): string
    {
        return 'client-request.paid';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->entrega->id,
            'status' => $this->entrega->status,
            'elapsed_minutes' => $this->entrega->minutosDesdeAceptacion(),
            'eta_minutes' => $this->entrega->minutosRestantes(),
        ];
    }
}
