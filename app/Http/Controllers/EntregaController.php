<?php

namespace App\Http\Controllers;

use App\Http\Requests\Entrega\CreateEntregaRequest;
use App\Services\EntregaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EntregaController extends Controller
{
    public function __construct(
        private EntregaService $entregaService
    ) {}

    /**
     * 📦 Crear entrega
     */
    public function store(CreateEntregaRequest $request): JsonResponse
    {
        $entrega = $this->entregaService->create(
            $request->validated(),
            $request->user()
        );

        return response()->json($entrega, 201);
    }

    /**
     * ❌ Cancelar entrega
     */
    public function cancelar(Request $request, int $id): JsonResponse
    {
        $entrega = $this->entregaService->cancelar(
            $id,
            $request->user(),
            $request->input('motivo')
        );

        return response()->json([
            'message' => 'Entrega cancelada correctamente',
            'entrega' => $entrega
        ]);
    }
}
