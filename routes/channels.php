<?php

use Illuminate\Support\Facades\Broadcast;

/*Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return $user && $user->rol === 'despachador';
});*/


Broadcast::channel('dashboard', function ($user) {
    logger('Broadcast auth user:', [$user]);
    return true;
});

