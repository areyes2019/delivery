<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureCliente;

use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    // 🛡️ MIDDLEWARES (Laravel 12)
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | Aliases
        |--------------------------------------------------------------------------
        */
        $middleware->alias([
            'jwt.auth' => JwtMiddleware::class,
            'cliente'  => EnsureCliente::class,
            'role'     => EnsureRole::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | WEB STACK (SESSION + CSRF)
        |--------------------------------------------------------------------------
        | ❗ NO Authenticate aquí
        */
        $middleware->web([
            EncryptCookies::class,
            StartSession::class,
            ValidateCsrfToken::class,
            SubstituteBindings::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | API STACK (SANCTUM STATEFUL)
        |--------------------------------------------------------------------------
        */
        $middleware->api([
            EnsureFrontendRequestsAreStateful::class,
            SubstituteBindings::class,
        ]);
    })

    // 🚨 EXCEPCIONES API
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (Throwable $e, $request) {

            if ($request->is('api/*')) {

                if ($e instanceof ValidationException) {
                    return response()->json([
                        'message' => 'Datos inválidos',
                        'errors' => $e->errors(),
                        'statusCode' => 422,
                    ], 422);
                }

                if ($e instanceof HttpExceptionInterface) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'statusCode' => $e->getStatusCode(),
                    ], $e->getStatusCode());
                }

                return response()->json([
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'Error interno del servidor',
                    'statusCode' => 500,
                ], 500);
            }
        });
    })

    ->create();
