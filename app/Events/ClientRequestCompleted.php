<?php

namespace App\Events;

use App\Models\ClientRequest;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class ClientRequestCompleted implements ShouldBroadcast, ShouldQueue
{
    use Queueable, SerializesModels;

    public ClientRequest $entrega;

    public function __construct(ClientRequest $entrega)
    {
        $this->entrega = $entrega;
    }

    public function broadcastOn()
    {
        return new PrivateChannel(
            'driver.'.$this->entrega->driver_id
        );
    }

    public function broadcastAs(): string
    {
        return 'client-request.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->entrega->id,
            'status' => $this->entrega->status,
        ];
    }
}
