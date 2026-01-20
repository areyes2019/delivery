<?php

namespace App\Http\Controllers;

use App\Models\ClientRequest;
use App\Services\ClientRequestService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ClientRequest\CreateClientRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClientRequestController extends Controller
{
    public function __construct(
        private ClientRequestService $clientRequestService
    ) {}

    /*
    |------------------------------------------------------------------
    | DESPACHADOR
    |------------------------------------------------------------------
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

        return response()->json([
            'message' => 'Entrega creada correctamente',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
                'destinatario' => $clientRequest->destinatario_nombre,
                'destination_description' => $clientRequest->destination_description,
                'created_at' => $clientRequest->created_at,
            ],
        ], 201);
    }

    /**
     * 📋 Dashboard general (sidebar + driver state)
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        $requests = ClientRequest::with('driver:id,name')
            ->deCliente($user->cliente_id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($requests);
    }

    /**
     * 🚚 Envíos en proceso (DriverState)
     */
    public function enProceso(Request $request): JsonResponse
    {
        $user = $request->user();

        $requests = ClientRequest::with('driver:id,name')
            ->deCliente($user->cliente_id)
            ->enProceso()
            ->orderByDesc('created_at')
            ->get();

        return response()->json($requests);
    }

    /*
    |------------------------------------------------------------------
    | DRIVER
    |------------------------------------------------------------------
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

        return response()->json([
            'message' => 'Solicitud aceptada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
            ],
        ]);
    }

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

        return response()->json([
            'message' => 'Entrega iniciada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
                'started_at' => $clientRequest->started_at,
            ],
        ]);
    }

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

        return response()->json([
            'message' => 'Entrega marcada como pagada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
                'paid_at' => $clientRequest->paid_at,
            ],
        ]);
    }

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

        return response()->json([
            'message' => 'Entrega finalizada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
                'delivered_at' => $clientRequest->delivered_at,
            ],
        ]);
    }
}
