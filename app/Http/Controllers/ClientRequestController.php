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
    |--------------------------------------------------------------------------
    | DESPACHADOR
    |--------------------------------------------------------------------------
    */

    /**
     * ➕ Crear nueva solicitud (Despachador)
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
                'destinatario' => $clientRequest->destinatario_nombre,
                'destination_description' => $clientRequest->destination_description,
                'created_at' => $clientRequest->created_at->toISOString(),
            ],
        ], 201);
    }

    /**
     * 📋 Dashboard general (Despachador)
     */
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
            function () use ($user) {
                return ClientRequest::with('driver:id,name')
                    ->deCliente($user->cliente_id)
                    ->orderByDesc('created_at')
                    ->get();
            }
        );

        return response()->json($requests);
    }

    /**
     * 🚚 Envíos en proceso (Despachador)
     */
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
            function () use ($user) {
                return ClientRequest::with('driver:id,name')
                    ->deCliente($user->cliente_id)
                    ->enProceso()
                    ->orderByDesc('created_at')
                    ->get();
            }
        );

        return response()->json($requests);
    }

    /*
    |--------------------------------------------------------------------------
    | DRIVER
    |--------------------------------------------------------------------------
    */

    public function disponibles(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->rol !== 'driver') {
            throw new HttpException(403, 'Solo drivers pueden ver solicitudes');
        }

        $requests = ClientRequest::where(
                'status',
                EntregaStatus::CREATED->value
            )
            ->orderBy('created_at', 'desc')
            ->get([
                'id',
                'pickup_description',
                'destination_description',
                'fare_offered',
                'status',
                'created_at',
            ]);

        return response()->json($requests);
    }

    /**
     * ✅ Aceptar solicitud (Driver)
     */
    public function accept(Request $request, int $id): JsonResponse
    {
        $driver = $request->user();

        if ($driver->rol !== 'driver') {
            throw new HttpException(403, 'Solo drivers pueden aceptar solicitudes');
        }

        $clientRequest = ClientRequest::findOrFail($id);

        try {
            $clientRequest->marcarComoAceptada($driver);
        } catch (\LogicException $e) {
            throw new HttpException(409, $e->getMessage());
        }

        Cache::forget("dashboard_requests_cliente_{$clientRequest->cliente_id}");
        Cache::forget("en_proceso_cliente_{$clientRequest->cliente_id}");

        // 🔔 EVENTO ACCEPTED
        event(
            new ClientRequestAccepted(
                $clientRequest->fresh('driver')
            )
        );

        return response()->json([
            'message' => 'Solicitud aceptada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
            ],
        ]);
    }

    /**
     * ▶️ Iniciar entrega (Driver)
     */
    public function start(Request $request, int $id): JsonResponse
    {
        $driver = $request->user();

        if ($driver->rol !== 'driver') {
            throw new HttpException(403, 'Solo drivers pueden iniciar la entrega');
        }

        $clientRequest = ClientRequest::findOrFail($id);

        try {
            $clientRequest->iniciarEntrega($driver);
        } catch (\LogicException $e) {
            throw new HttpException(409, $e->getMessage());
        }

        Cache::forget("dashboard_requests_cliente_{$clientRequest->cliente_id}");
        Cache::forget("en_proceso_cliente_{$clientRequest->cliente_id}");

        // 🔔 EVENTO PICKED_UP
        event(
            new ClientRequestPickedUp(
                $clientRequest->fresh('driver')
            )
        );

        return response()->json([
            'message' => 'Entrega iniciada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
                'started_at' => optional($clientRequest->started_at)->toISOString(),
            ],
        ]);
    }

    /**
     * 💰 Marcar como pagada (Driver)
     */
    public function pay(Request $request, int $id): JsonResponse
    {
        $driver = $request->user();

        if ($driver->rol !== 'driver') {
            throw new HttpException(403, 'Solo drivers pueden marcar como pagada');
        }

        $clientRequest = ClientRequest::findOrFail($id);

        try {
            $clientRequest->marcarComoPagada();
        } catch (\LogicException $e) {
            throw new HttpException(409, $e->getMessage());
        }

        Cache::forget("dashboard_requests_cliente_{$clientRequest->cliente_id}");
        Cache::forget("en_proceso_cliente_{$clientRequest->cliente_id}");

        // 🔔 EVENTO PAID
        event(
            new ClientRequestPaid(
                $clientRequest->fresh('driver')
            )
        );

        return response()->json([
            'message' => 'Entrega marcada como pagada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
                'paid_at' => optional($clientRequest->paid_at)->toISOString(),
            ],
        ]);
    }

    /**
     * 🏁 Completar entrega (Driver)
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        $driver = $request->user();

        if ($driver->rol !== 'driver') {
            throw new HttpException(403, 'Solo drivers pueden marcar como entregada');
        }

        $clientRequest = ClientRequest::findOrFail($id);

        try {
            $clientRequest->marcarComoEntregada();
        } catch (\LogicException $e) {
            throw new HttpException(409, $e->getMessage());
        }

        Cache::forget("dashboard_requests_cliente_{$clientRequest->cliente_id}");
        Cache::forget("en_proceso_cliente_{$clientRequest->cliente_id}");

        // 🔔 EVENTO COMPLETED
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
