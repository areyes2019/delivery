<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\EnsureRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    // 🛡️ Middlewares (Laravel 12)
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt.auth' => JwtMiddleware::class,
            'cliente'  => \App\Http\Middleware\EnsureCliente::class,
            'role'     => EnsureRole::class, // 👈 ESTA ES LA CLAVE
        ]);
    })

    // 🚨 Manejo de excepciones (API first, seguro)
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {

            // Solo interceptamos API
            if ($request->is('api/*')) {

                // ❌ Validaciones
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'message' => 'Datos inválidos',
                        'errors'  => $e->errors(),
                        'statusCode' => 422
                    ], 422);
                }

                // ❌ Errores HTTP (403, 404, etc.)
                if ($e instanceof HttpExceptionInterface) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'statusCode' => $e->getStatusCode(),
                    ], $e->getStatusCode());
                }

                // ❌ Error genérico (no filtrar detalles en prod)
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
