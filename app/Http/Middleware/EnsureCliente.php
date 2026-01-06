<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCliente
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // 🛑 No autenticado
        if (!$user) {
            // Si es web, puedes redirigir
            return response()->json([
                'message' => 'No autenticado'
            ], 401);
        }

        // 🛑 Usuario sin cliente asignado
        if (!$user->cliente_id && $user->rol !== 'superadmin') {
            return response()->json([
                'message' => 'Usuario sin cliente asignado'
            ], 403);
        }

        return $next($request);
    }
}
