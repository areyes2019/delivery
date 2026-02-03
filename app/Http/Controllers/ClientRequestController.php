<?php

namespace App\Http\Controllers;

use App\Models\ClientRequest;
use App\Services\ClientRequestService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ClientRequest\CreateClientRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Cache;
use App\Enums\EntregaStatus;

/* 🔔 Eventos */
use App\Events\ClientRequestAccepted;
use App\Events\ClientRequestPickedUp;
use App\Events\ClientRequestPaid;
use App\Events\ClientRequestCompleted;

class ClientRequestController extends Controller
{
    public function __construct(
        private ClientRequestService $clientRequestService
    ) {}

    /*
    |--------------------------------------------------------------------------|
    | DESPACHADOR
    |--------------------------------------------------------------------------|
    */

    public function store(CreateClientRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->rol !== 'despachador') {
            throw new HttpException(403, 'Solo despachadores pueden crear solicitudes');
        }

        $id = $this->clientRequestService->create(
            $request->validated(),
            $user
        );

        $clientRequest = ClientRequest::findOrFail($id);

        Cache::forget("dashboard_requests_cliente_{$user->cliente_id}");

        return response()->json([
            'message' => 'Entrega creada correctamente',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
                'destinatario_nombre' => $clientRequest->destinatario_nombre,
                'pickup_description' => $clientRequest->pickup_description,
                'destination_description' => $clientRequest->destination_description,
                'fare_offered' => $clientRequest->fare_offered,
                'created_at' => $clientRequest->created_at->toISOString(),
            ],
        ], 201);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->rol !== 'despachador') {
            throw new HttpException(403, 'Acceso solo para despachadores');
        }

        $cacheKey = "dashboard_requests_cliente_{$user->cliente_id}";

        $requests = Cache::remember(
            $cacheKey,
            5,
            fn () => ClientRequest::with('driver:id,name')
                ->deCliente($user->cliente_id)
                ->orderByDesc('created_at')
                ->get()
        );

        return response()->json($requests);
    }

    public function enProceso(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->rol !== 'despachador') {
            throw new HttpException(403, 'Acceso solo para despachadores');
        }

        $cacheKey = "en_proceso_cliente_{$user->cliente_id}";

        $requests = Cache::remember(
            $cacheKey,
            5,
            fn () => ClientRequest::with('driver:id,name')
                ->deCliente($user->cliente_id)
                ->enProceso()
                ->orderByDesc('created_at')
                ->get()
        );

        return response()->json($requests);
    }

    /*
    |--------------------------------------------------------------------------|
    | DRIVER
    |--------------------------------------------------------------------------|
    */

    public function disponibles(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->rol !== 'driver') {
            throw new HttpException(403, 'Solo drivers pueden ver solicitudes');
        }

        return response()->json(
            ClientRequest::where('status', EntregaStatus::CREATED->value)
                ->orderByDesc('created_at')
                ->get([
                    'id',
                    'pickup_description',
                    'destination_description',
                    'fare_offered',
                    'status',
                    'created_at',
                ])
        );
    }

    /**
     * ✅ CREATED → ACCEPTED
     */
    public function accept(Request $request, int $id): JsonResponse
    {
        $driver = $request->user();

        if ($driver->rol !== 'driver') {
            throw new HttpException(403);
        }

        $tieneActiva = ClientRequest::where('driver_id', $driver->id)
            ->whereIn('status', [
                EntregaStatus::ACCEPTED->value,
                EntregaStatus::PICKED_UP->value,
                EntregaStatus::PAID->value,
            ])
            ->exists();

        if ($tieneActiva) {
            return response()->json([
                'message' => 'Ya tienes una solicitud activa'
            ], 422);
        }

        $clientRequest = ClientRequest::where('id', $id)
            ->where('status', EntregaStatus::CREATED->value)
            ->firstOrFail();

        $clientRequest->marcarComoAceptada($driver);

        Cache::forget("dashboard_requests_cliente_{$clientRequest->cliente_id}");
        Cache::forget("en_proceso_cliente_{$clientRequest->cliente_id}");

        broadcast(
            new ClientRequestAccepted(
                $clientRequest->fresh('driver')
            )
        )->toOthers();

        return response()->json([
            'message' => 'Solicitud aceptada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
            ],
        ]);
    }

    /**
     * 📦 ACCEPTED → PICKED_UP
     * (Confirmar recogida en Flutter)
     */
    public function start(Request $request, int $id): JsonResponse
    {
        $driver = $request->user();
        $clientRequest = ClientRequest::findOrFail($id);

        if ($driver->rol !== 'driver' || $clientRequest->driver_id !== $driver->id) {
            throw new HttpException(403);
        }

        if ($clientRequest->status !== EntregaStatus::ACCEPTED->value) {
            throw new HttpException(409, 'Estado inválido para iniciar');
        }

        $clientRequest->iniciarEntrega($driver);

        Cache::forget("dashboard_requests_cliente_{$clientRequest->cliente_id}");
        Cache::forget("en_proceso_cliente_{$clientRequest->cliente_id}");

        event(
            new ClientRequestPickedUp(
                $clientRequest->fresh('driver')
            )
        );

        return response()->json([
            'message' => 'Recogida confirmada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
                'started_at' => optional($clientRequest->started_at)->toISOString(),
            ],
        ]);
    }

    /**
     * 💰 PICKED_UP → PAID
     */
    public function pay(Request $request, int $id): JsonResponse
    {
        $driver = $request->user();
        $clientRequest = ClientRequest::findOrFail($id);

        if ($driver->rol !== 'driver' || $clientRequest->driver_id !== $driver->id) {
            throw new HttpException(403);
        }

        if ($clientRequest->status !== EntregaStatus::PICKED_UP->value) {
            throw new HttpException(409, 'La entrega aún no ha sido recogida');
        }

        $clientRequest->marcarComoPagada();

        Cache::forget("dashboard_requests_cliente_{$clientRequest->cliente_id}");
        Cache::forget("en_proceso_cliente_{$clientRequest->cliente_id}");

        event(
            new ClientRequestPaid(
                $clientRequest->fresh('driver')
            )
        );

        return response()->json([
            'message' => 'Entrega cobrada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
                'paid_at' => optional($clientRequest->paid_at)->toISOString(),
            ],
        ]);
    }

    /**
     * 🏁 PAID → DELIVERED
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        $driver = $request->user();
        $clientRequest = ClientRequest::findOrFail($id);

        if ($driver->rol !== 'driver' || $clientRequest->driver_id !== $driver->id) {
            throw new HttpException(403);
        }

        if ($clientRequest->status !== EntregaStatus::PAID->value) {
            throw new HttpException(409, 'La entrega debe estar pagada');
        }

        $clientRequest->marcarComoEntregada();

        Cache::forget("dashboard_requests_cliente_{$clientRequest->cliente_id}");
        Cache::forget("en_proceso_cliente_{$clientRequest->cliente_id}");

        event(
            new ClientRequestCompleted(
                $clientRequest->fresh('driver')
            )
        );

        return response()->json([
            'message' => 'Entrega finalizada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
                'delivered_at' => optional($clientRequest->delivered_at)->toISOString(),
            ],
        ]);
    }
}
