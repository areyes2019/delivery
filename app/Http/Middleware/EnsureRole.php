<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();

        if (!$user || $user->rol !== $role) {
            return response()->json([
                'message' => 'No autorizado'
            ], 403);
        }

        return $next($request);
    }
}
