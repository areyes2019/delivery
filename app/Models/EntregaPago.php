<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EntregaPago extends Model
{
    use HasFactory;

    protected $table = 'entrega_pagos';

    protected $fillable = [
        'client_request_id',
        'entrega_id',

        'payment_method',       // CASH | CARD
        'amount_received',

        'received_by_user_id',

        'status',               // PENDING | CONFIRMED | REJECTED
        'paid_at',

        'reference',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    // 🔗 Relación con Entrega
    public function entrega()
    {
        return $this->belongsTo(Entrega::class);
    }

    // 🔗 Relación con ClientRequest
    public function clientRequest()
    {
        return $this->belongsTo(ClientRequest::class);
    }
}
