<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClientRequestController;
use App\Http\Controllers\DriverTestController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Auth;
use App\Events\ClientRequestAccepted;
use App\Models\ClientRequest;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
| delivery.local
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return redirect('/login');
});

/*
|--------------------------------------------------------------------------
| AUTH WEB
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| DASHBOARD WEB
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->name('dashboard.admin');

    Route::get('/dashboard/despachador', [DashboardController::class, 'despachador'])
        ->name('dashboard.despachador');

});


/*
|--------------------------------------------------------------------------
| PANEL CLIENTE (WEB / API MIXTO)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'cliente'])
    ->prefix('panel')
    ->group(function () {

        Route::get('/entregas', [ClientRequestController::class, 'index']);
        Route::post('/entregas', [ClientRequestController::class, 'store']);
        Route::get('/entregas/{id}', [ClientRequestController::class, 'show']);
    });

/*SOLO PARA PRUEBAS*/
Route::middleware(['auth', 'role:driver'])->group(function () {

    Route::get('/driver', [DriverTestController::class, 'index']);
    Route::get('/driver/{id}', [DriverTestController::class, 'show']);

    Route::post('/driver/{id}/accept', [DriverTestController::class, 'accept']);
    Route::post('/driver/{id}/start', [DriverTestController::class, 'start']);
    Route::post('/driver/{id}/pay', [DriverTestController::class, 'pay']);
    Route::post('/driver/{id}/complete', [DriverTestController::class, 'complete']);

});

Route::get('/test-broadcast', function() {
    // Crea una solicitud de prueba si no hay
    if (!ClientRequest::count()) {
        $request = ClientRequest::create([
            'cliente_id' => 1,
            'status' => 'CREATED',
            'destinatario_nombre' => 'Test User',
            'fare_offered' => 100,
        ]);
    } else {
        $request = ClientRequest::first();
    }
    
    // Dispara el evento
    event(new ClientRequestAccepted($request));
    
    return response()->json([
        'message' => 'Evento enviado',
        'request_id' => $request->id,
        'channel' => 'dashboard'
    ]);
});

/*
|--------------------------------------------------------------------------
| DESPACHADOR
|--------------------------------------------------------------------------
*/
/*Route::middleware(['web', 'auth', 'role:despachador'])->group(function () {

    Route::post('/client-requests', [ClientRequestController::class, 'store']);
    Route::get('/client-requests', [ClientRequestController::class, 'index']);
    Route::get('/client-requests/{id}', [ClientRequestController::class, 'show']);

    Route::get('/dashboard/map', [DashboardController::class, 'map']);

    Route::get(
        '/dashboard/client-requests',
        [ClientRequestController::class, 'dashboard']
    );
});*/
