<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\LoginRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function __construct(private UserService $userService) {

    }

    public function login(LoginRequest $request): JsonResponse {
        $response = $this->userService->login($request->validated());
        return response()->json($response, 200);
    }

    public function register(CreateUserRequest $request) {
        $user = $this->userService->create($request->validated());
        return response()->json($user, status: 201);
    }
    
}
