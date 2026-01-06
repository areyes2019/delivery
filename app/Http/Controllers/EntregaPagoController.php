<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\EntregaPago;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EntregaPagoController extends Controller
{
    /**
     * 💰 Crear pago (driver)
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'entrega_id'      => 'required|exists:entregas,id',
            'payment_method'  => 'required|in:CASH,CARD',
            'amount_received' => 'required|numeric|min:0',
            'reference'       => 'nullable|string',
        ]);

        $driver = $request->user();

        // 🔥 Cargar entrega CON clientRequest
        $entrega = Entrega::with('clientRequest')
            ->findOrFail($request->entrega_id);

        if (!$entrega->clientRequest) {
            throw new HttpException(422, 'La entrega no tiene solicitud asociada');
        }

        // 🔐 Solo el driver asignado puede cobrar
        if ($entrega->driver_id !== $driver->id) {
            throw new HttpException(403, 'No puedes cobrar esta entrega');
        }

        $clientRequest = $entrega->clientRequest;

        // 🚫 Evitar doble pago
        if ($clientRequest->entregaPago) {
            throw new HttpException(422, 'El pago ya fue registrado');
        }

        // 🔐 Validar monto exacto
        if ((float)$request->amount_received !== (float)$clientRequest->total_expected) {
            throw new HttpException(
                422,
                'El monto recibido no coincide con el total esperado'
            );
        }

        // ✅ CREAR PAGO (CLAVE: client_request_id)
        $pago = EntregaPago::create([
            'client_request_id'   => $clientRequest->id, // 🔥 ESTE CAMPO
            'entrega_id'          => $entrega->id,
            'payment_method'      => $request->payment_method,
            'amount_received'     => $request->amount_received,
            'received_by_user_id' => $driver->id,
            'status'              => 'CONFIRMED',
            'paid_at'             => now(),
            'reference'           => $request->reference,
        ]);

        return response()->json([
            'message' => 'Pago registrado correctamente',
            'pago'    => $pago,
        ], 201);
    }


    /**
     * 💵 Cobrar pago existente (efectivo)
     */
    public function cobrar(Request $request, EntregaPago $pago): JsonResponse
    {
        // 🚫 Ya pagado
        if ($pago->status === 'CONFIRMED') {
            return response()->json([
                'message' => 'Este pago ya fue cobrado.'
            ], 422);
        }

        // 🚫 Solo efectivo
        if ($pago->payment_method !== 'CASH') {
            return response()->json([
                'message' => 'Solo los pagos en efectivo pueden cobrarse manualmente.'
            ], 422);
        }

        // 🔐 Driver asignado
        if ($pago->entrega->driver_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para cobrar este pago.'
            ], 403);
        }

        // ✅ Confirmar pago
        $pago->update([
            'status'   => 'CONFIRMED',
            'paid_at'  => now(),
        ]);

        return response()->json([
            'message' => 'Pago cobrado correctamente.',
            'pago'    => $pago
        ]);
    }
}
