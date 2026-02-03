<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| DESPACHADOR – DASHBOARD CLIENTE
|--------------------------------------------------------------------------
*/
Broadcast::channel('dashboard.cliente.{clienteId}', function ($user, $clienteId) {
    return $user->cliente_id == (int) $clienteId
        && $user->rol === 'despachador';
});

/*
|--------------------------------------------------------------------------
| DRIVER – CANAL PRIVADO
|--------------------------------------------------------------------------
*/
Broadcast::channel('driver.{driverId}', function ($user, $driverId) {
    return $user->id === (int) $driverId
        && $user->rol === 'driver';
});
