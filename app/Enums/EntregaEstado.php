<?php

namespace App\Enums;

enum EntregaEstado: string
{
    case CREADA     = 'CREADA';
    case ASIGNADA   = 'ASIGNADA';
    case EN_CAMINO  = 'EN_CAMINO';
    case ENTREGADA  = 'ENTREGADA';
    case CANCELADA  = 'CANCELADA';
}
