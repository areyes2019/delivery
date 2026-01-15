<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entrega\ChangeEntregaEstadoRequest;
use App\Models\Entrega;
use App\Services\EntregaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Enums\EntregaEstado;
use Symfony\Component\HttpKernel\Exception\HttpException;
class EntregaDriverController extends Controller
{
    public function __construct(
        private EntregaService $entregaService
    ) {}

    /**
     * 🚗 Driver acepta la entrega
     * CREADA → ASIGNADA
     */
    public function accept(int $id, Request $request): JsonResponse
    {
        $driver = $request->user();

        $entrega = Entrega::findOrFail($id);

        $entrega = $this->entregaService->acceptEntrega(
            $entrega,
            $driver
        );

        return response()->json([
            'message' => 'Entrega aceptada',
            'entrega' => $entrega,
        ]);
    }

    /**
     * 🔄 Driver cambia estado
     * ASIGNADA → EN_CAMINO → ENTREGADA
     */
    public function changeEstado(Request $request)
    {
        $estado = EntregaEstado::tryFrom($request->estado);

        if (!$estado) {
            throw new HttpException(422, 'Estado inválido');
        }

        $entrega = $this->entregaService->changeEstado(
            $request->entrega_id,
            $estado, // ENUM ✅
            $request->user()
        );

        return response()->json([
            'message' => 'Estado actualizado',
            'entrega' => [
                'id'     => $entrega->id,
                'estado' => $entrega->estado->value,
            ],
        ]);
    }
}
