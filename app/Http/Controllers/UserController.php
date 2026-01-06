<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\User\CreateDespachadorRequest;
use App\Http\Requests\User\CreateAdminEmpresaRequest;
use App\Http\Requests\User\CreateDriverRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * 👁️ Ver usuario por ID (según jerarquía)
     */
    public function show(
        int $id,
        Request $request
    ): JsonResponse {
        $user = $this->userService->findByIdForUser(
            $id,
            $request->user()
        );

        return response()->json($user);
    }

    /**
     * ✏️ Actualizar usuario (según rol)
     */
    public function update(
        int $id,
        UpdateUserRequest $request
    ): JsonResponse {
        $user = $this->userService->updateByRole(
            $id,
            $request->validated(),
            $request->user()
        );

        return response()->json($user);
    }

    /**
     * 🔱 Crear Admin Cliente (solo SuperAdmin)
     */
    public function storeAdminEmpresa(
        CreateAdminEmpresaRequest $request
    ): JsonResponse {
        $admin = $this->userService->createAdminCliente(
            $request->validated(),
            $request->user()
        );

        return response()->json($admin, 201);
    }

    /**
     * 🧑‍💼 Crear Driver (solo Admin Cliente)
     */
    
    public function storeDriver(
        CreateDriverRequest $request
    ): JsonResponse {
        $driver = $this->userService->createDriver(
            $request->validated(),
            $request->user()
        );

        return response()->json($driver, 201);
    }
    public function assignDriverToFlotilla(
        Request $request,
        int $driverId
    ): JsonResponse {

        $data = $request->validate([
            'flotilla_id' => 'required|integer'
        ]);

        $driver = $this->userService->assignDriverToFlotilla(
            $driverId,
            $data['flotilla_id'],
            $request->user()
        );

        return response()->json([
            'id'          => $driver->id,
            'flotilla_id' => $driver->flotilla_id
        ]);
    }
}
