<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\LoginRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct(private UserService $userService) {}

    /**
     * 🔐 LOGIN API (Postman / Flutter)
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        if (! $user->activo) {
            return response()->json(['message' => 'Usuario inactivo'], 403);
        }

        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken,
            'user'  => [
                'id'    => $user->id,
                'rol'   => $user->rol,
                'email'=> $user->email,
                'name' => $user->name,
            ],
        ]);
    }

    /**
     * 📝 REGISTRO PÚBLICO
     */
    public function register(CreateUserRequest $request): JsonResponse
    {
        $user = $this->userService->registerPublic(
            $request->validated()
        );

        return response()->json([
            'message' => 'Registro exitoso. Tu cuenta está pendiente de aprobación.',
            'user_id' => $user->id,
        ], 201);
    }
}
