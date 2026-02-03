<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\LoginRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct(private UserService $userService) {}

    /**
     * 🔐 LOGIN API (Postman / Flutter)
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        $phone = preg_replace('/\D/', '', $request->phone); // solo números

        $user = User::where('phone', $phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        if (!$user->activo) {
            return response()->json([
                'message' => 'Usuario inactivo'
            ], 403);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'=>[
                'id'=> $user->id,
                'name'=> $user->name,
                'phone'=> $user->phone,
                'role'=> $user->role,
            ]
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
