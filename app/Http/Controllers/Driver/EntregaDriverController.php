<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\ClientRequest;
use App\Enums\EntregaStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EntregaDriverController extends Controller
{
    /**
     * 📋 TABLERO DEL DRIVER
     * Solicitudes disponibles (CREATED)
     */
    public function tablero(Request $request): JsonResponse
    {
        $entregas = ClientRequest::query()
            ->where('status', EntregaStatus::CREATED->value)
            ->whereNull('driver_id')
            ->orderBy('created_at')
            ->get();

        return response()->json($entregas);
    }

    /**
     * 🚚 ENTREGA ACTIVA DEL DRIVER
     */
    public function actual(Request $request): JsonResponse
    {
        $entrega = ClientRequest::where('driver_id', $request->user()->id)
            ->whereIn('status', [
                EntregaStatus::ACCEPTED->value,
                EntregaStatus::PICKED_UP->value,
                EntregaStatus::PAID->value,
            ])
            ->first();

        return response()->json($entrega);
    }

    /**
     * ✅ ACEPTAR ENTREGA
     * CREATED → ACCEPTED
     */
    public function aceptar(int $id, Request $request): JsonResponse
    {
        $driver = $request->user();

        $entrega = DB::transaction(function () use ($id, $driver) {
            $entrega = ClientRequest::lockForUpdate()->findOrFail($id);

            if (! $entrega->puedeSerAceptada()) {
                throw new HttpException(409, 'La solicitud no puede ser aceptada');
            }

            $activa = ClientRequest::where('driver_id', $driver->id)
                ->whereIn('status', [
                    EntregaStatus::ACCEPTED->value,
                    EntregaStatus::PICKED_UP->value,
                    EntregaStatus::PAID->value,
                ])
                ->exists();

            if ($activa) {
                throw new HttpException(409, 'Ya tienes una entrega activa');
            }

            $entrega->marcarComoAceptada($driver);

            return $entrega;
        });

        return response()->json($entrega);
    }

    /**
     * ▶️ INICIAR ENTREGA
     * ACCEPTED → PICKED_UP
     */
    public function iniciar(int $id, Request $request): JsonResponse
    {
        $entrega = ClientRequest::findOrFail($id);
        $entrega->iniciarEntrega($request->user());

        return response()->json($entrega);
    }

    /**
     * 💰 COBRAR
     * PICKED_UP → PAID
     */
    public function cobrar(int $id, Request $request): JsonResponse
    {
        $entrega = ClientRequest::findOrFail($id);
        $entrega->marcarComoPagada();

        return response()->json($entrega);
    }

    /**
     * 📦 ENTREGAR
     * PAID → DELIVERED
     */
    public function entregar(int $id, Request $request): JsonResponse
    {
        $entrega = ClientRequest::findOrFail($id);
        $entrega->marcarComoEntregada();

        return response()->json($entrega);
    }
}
