<?php

namespace App\Http\Controllers;

use App\Http\Requests\Flotilla\CreateFlotillaRequest;
use App\Services\FlotillaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FlotillaController extends Controller
{
    public function __construct(
        private FlotillaService $flotillaService
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * 📄 Listar flotillas del cliente
     */
    public function index(Request $request): JsonResponse
    {
        $flotillas = $this->flotillaService->listByCliente(
            $request->user()
        );

        return response()->json($flotillas);
    }

    /**
     * ➕ Crear flotilla
     */
    public function store(
        CreateFlotillaRequest $request
    ): JsonResponse {
        $flotilla = $this->flotillaService->create(
            $request->validated(),
            $request->user()
        );

        return response()->json($flotilla, 201);
    }

    /**
     * 🔒 Activar / Desactivar flotilla
     */
    public function toggle(
        Request $request,
        int $id
    ): JsonResponse {
        $flotilla = $this->flotillaService->toggleStatus(
            $id,
            $request->user()
        );

        return response()->json([
            'id'     => $flotilla->id,
            'activo' => $flotilla->activo,
        ]);
    }
}
