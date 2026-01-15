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

    /**
     * ➕ Crear solicitud de entrega
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

        // 👉 Payload listo para dashboard
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
     * 📋 Listar solicitudes del cliente
     */
    public function index(Request $request): JsonResponse
    {
        $requests = ClientRequest::deCliente($request->user()->cliente_id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($requests);
    }

    /**
     * 👁️ Ver una solicitud
     */
    public function show(int $id): JsonResponse
    {
        return response()->json(
            ClientRequest::findOrFail($id)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DRIVER
    |--------------------------------------------------------------------------
    */

    /**
     * ✅ Aceptar solicitud
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

    /**
     * 🚀 Iniciar entrega → EN_CAMINO
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

        return response()->json([
            'message' => 'Entrega iniciada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
                'started_at' => $clientRequest->started_at,
            ],
        ]);
    }

    /**
     * 💰 Marcar como pagada → PAGADA
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

        return response()->json([
            'message' => 'Entrega marcada como pagada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
                'paid_at' => $clientRequest->paid_at,
            ],
        ]);
    }

    /**
     * 🏁 Marcar como entregada → ENTREGADA
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

        return response()->json([
            'message' => 'Entrega finalizada',
            'data' => [
                'id' => $clientRequest->id,
                'status' => $clientRequest->status,
                'delivered_at' => $clientRequest->delivered_at,
            ],
        ]);
    }
    public function available(){
        return "Saludos";
    }
}
