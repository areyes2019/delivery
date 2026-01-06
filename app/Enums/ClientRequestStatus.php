<?php

namespace App\Enums;

enum ClientRequestStatus: string
{
    case CREATED  = 'CREATED';
    case ACCEPTED = 'ACCEPTED';
    case CANCELLED = 'CANCELLED';
}
