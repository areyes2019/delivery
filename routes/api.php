<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientRequestController;
use App\Http\Controllers\DriverCarInfoController;
use App\Http\Controllers\DriverPositionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FlotillaController;
use App\Http\Controllers\EntregaPagoController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\Driver\EntregaDriverController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| USER SESSION
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN (NIVEL DIOS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {

    Route::get('/clientes', [ClienteController::class, 'index']);
    Route::post('/clientes', [ClienteController::class, 'store']);
    Route::get('/clientes/{id}', [ClienteController::class, 'show']);
    Route::put('/clientes/{id}', [ClienteController::class, 'update']);
    Route::patch('/clientes/{id}/toggle', [ClienteController::class, 'toggle']);

    Route::post('/admin-empresa', [UserController::class, 'storeAdminEmpresa']);
});

/*
|--------------------------------------------------------------------------
| FLOTILLAS (LECTURA)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin_cliente,despachador'])->group(function () {
    Route::get('/flotillas', [FlotillaController::class, 'index']);
    Route::get('/flotillas/{id}', [FlotillaController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| ADMIN CLIENTE (GESTIÓN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin_cliente'])->group(function () {

    Route::post('/flotillas', [FlotillaController::class, 'store']);
    Route::put('/flotillas/{id}', [FlotillaController::class, 'update']);
    Route::patch('/flotillas/{id}/toggle', [FlotillaController::class, 'toggle']);

    Route::post('/drivers', [UserController::class, 'storeDriver']);
    Route::post('/despachadores', [UserController::class, 'storeDespachador']);

    Route::patch(
        '/drivers/{driverId}/assign-flotilla',
        [UserController::class, 'assignDriverToFlotilla']
    );
});

/*
|--------------------------------------------------------------------------
| DESPACHADOR
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:despachador'])->group(function () {

    Route::post('/client-requests', [ClientRequestController::class, 'store']);
    Route::get('/client-requests', [ClientRequestController::class, 'index']);
    Route::get('/client-requests/{id}', [ClientRequestController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| DRIVER
|--------------------------------------------------------------------------
*/
Route::prefix('driver')
    ->middleware(['auth:sanctum', 'role:driver'])
    ->group(function () {

        // Aceptar solicitud
        Route::post(
            '/client-requests/{id}/accept',
            [ClientRequestController::class, 'accept']
        );

        // Iniciar entrega → EN_CAMINO
        Route::post(
            '/client-requests/{id}/start',
            [ClientRequestController::class, 'start']
        );

        // Marcar como pagada → PAGADA
        Route::post(
            '/client-requests/{id}/pay',
            [ClientRequestController::class, 'pay']
        );

        // (legacy / otros módulos)
        Route::post('/entregas/estado', [EntregaDriverController::class, 'changeEstado']);

        Route::post('/entrega-pagos', [EntregaPagoController::class, 'store']);
        Route::post('/entrega-pagos/{pago}/cobrar', [EntregaPagoController::class, 'cobrar']);
        Route::post(
            '/client-requests/{id}/complete',
            [ClientRequestController::class, 'complete']
        );
});

/*
|--------------------------------------------------------------------------
| CANCELAR
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin_cliente,driver'])->group(function () {
    Route::post('/entregas/{id}/cancelar', [EntregaController::class, 'cancelar']);
});

/*
|--------------------------------------------------------------------------
| USERS
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/{id}', [UserController::class, 'findById']);
    Route::put('/users/{id}', [UserController::class, 'update']);
});
