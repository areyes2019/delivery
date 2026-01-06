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
    |--------------------------------------------------------------------------
    | DESPACHADOR
    |--------------------------------------------------------------------------
    */

    // Crear solicitud
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
            'id'     => $clientRequest->id,
            'status' => $clientRequest->status,
        ], 201);
    }

    // Listar solicitudes
    public function index(Request $request)
    {
        return ClientRequest::deCliente($request->user()->cliente_id)
            ->orderByDesc('created_at')
            ->get();
    }

    // Ver una solicitud
    public function show(int $id)
    {
        return ClientRequest::findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | DRIVER
    |--------------------------------------------------------------------------
    */

    // ✅ Aceptar solicitud
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
            'client_request' => [
                'id'     => $clientRequest->id,
                'status' => $clientRequest->status,
            ],
        ]);
    }

    // 🚀 Iniciar entrega → EN_CAMINO
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
            'client_request' => [
                'id'         => $clientRequest->id,
                'status'     => $clientRequest->status,
                'started_at' => $clientRequest->started_at,
            ],
        ]);
    }

    // 💰 MARCAR COMO PAGADA → PAGADA
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
            'client_request' => [
                'id'      => $clientRequest->id,
                'status'  => $clientRequest->status,
                'paid_at' => $clientRequest->paid_at,
            ],
        ]);
    }
    // 🏁 MARCAR COMO ENTREGADA
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
            'client_request' => [
                'id'           => $clientRequest->id,
                'status'       => $clientRequest->status,
                'delivered_at' => $clientRequest->delivered_at,
            ],
        ]);
    }

}
