<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClientRequestController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Auth;

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
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| PANEL CLIENTE (WEB / API MIXTO)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'cliente'])
    ->prefix('panel')
    ->group(function () {

        Route::get('/entregas', [ClientRequestController::class, 'index']);
        Route::post('/entregas', [ClientRequestController::class, 'store']);
        Route::get('/entregas/{id}', [ClientRequestController::class, 'show']);
    });
