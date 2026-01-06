<?php

namespace App\Enums;

enum EntregaStatus: string
{
    case CREATED   = 'CREATED';
    case ACCEPTED  = 'ACCEPTED';
    case PICKED_UP = 'PICKED_UP';
    case DELIVERED = 'DELIVERED';
    case PAID      = 'PAID';
    case CANCELED  = 'CANCELED';
}
