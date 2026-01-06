<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cliente\CreateClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Services\ClienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct(
        private ClienteService $clienteService
    ) {
        // 🔐 Todas las rutas requieren usuario autenticado
        $this->middleware('auth:sanctum');
    }

    /**
     * 📄 Listar clientes (solo SuperAdmin)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isSuperAdmin()) {
            abort(403, 'No autorizado');
        }

        return response()->json(
            $this->clienteService->listAll()
        );
    }

    /**
     * ➕ Crear cliente (solo SuperAdmin)
     */
    public function store(
        CreateClienteRequest $request
    ): JsonResponse {
        $cliente = $this->clienteService->create(
            $request->validated(),
            $request->user()
        );

        return response()->json($cliente, 201);
    }

    /**
     * 👁️ Ver cliente por ID (solo SuperAdmin)
     */
    public function show(
        Request $request,
        int $id
    ): JsonResponse {
        if (!$request->user()->isSuperAdmin()) {
            abort(403, 'No autorizado');
        }

        $cliente = $this->clienteService->findById($id);

        return response()->json($cliente);
    }

    /**
     * ✏️ Actualizar cliente (solo SuperAdmin)
     */
    public function update(
        UpdateClienteRequest $request,
        int $id
    ): JsonResponse {
        $cliente = $this->clienteService->update(
            $id,
            $request->validated(),
            $request->user()
        );

        return response()->json($cliente);
    }

    /**
     * 🔒 Activar / Desactivar cliente (solo SuperAdmin)
     */
    public function toggle(
        Request $request,
        int $id
    ): JsonResponse {
        $cliente = $this->clienteService->toggleStatus(
            $id,
            $request->user()
        );

        return response()->json([
            'id'     => $cliente->id,
            'activo' => $cliente->activo,
        ]);
    }
}
