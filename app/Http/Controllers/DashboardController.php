<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    
    /**
     * 🗺️ Mapa operativo del dashboard
     */
    public function map(Request $request): JsonResponse
    {
        $user = $request->user();

        // 🔐 Bloqueo por rol
        if (!in_array($user->rol, ['despachador', 'admin_cliente', 'superadmin'])) {
            return response()->json([
                'message' => 'No autorizado'
            ], 403);
        }

        $drivers = DB::table('drivers_position as dp')
            ->join('users as u', 'u.id', '=', 'dp.driver_id')
            ->leftJoin('entregas as e', function ($join) {
                $join->on('e.driver_id', '=', 'u.id')
                    ->whereIn('e.estado', ['ASIGNADA', 'EN_CAMINO']);
            })
            ->select([
                'u.id as driver_id',
                DB::raw("CONCAT(u.name, ' ', u.lastname) as driver_name"),
                DB::raw('ST_Y(dp.position) as lat'),
                DB::raw('ST_X(dp.position) as lng'),
                'dp.is_active',
                'e.id as entrega_id',
                'e.estado as estado_entrega',
            ])
            ->where('dp.is_active', true)
            ->get();

        return response()->json($drivers);
    }

}
