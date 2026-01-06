<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientRequestController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'cliente'])
    ->prefix('panel')
    ->group(function () {

        Route::get('/entregas', [ClientRequestController::class, 'index']);
        Route::post('/entregas', [ClientRequestController::class, 'store']);
        Route::get('/entregas/{id}', [ClientRequestController::class, 'show']);

});

