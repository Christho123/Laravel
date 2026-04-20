<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Login\LoginRequest;
use App\Http\Requests\Logout\LogoutRequest;
use App\Http\Requests\Refresh\RefreshRequest;
use App\Http\Requests\Register\RegisterRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'data' => $result,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'data' => $result,
        ]);
    }

    public function refresh(RefreshRequest $request): JsonResponse
    {
        $result = $this->authService->refresh($request->validated()['refresh_token']);

        return response()->json([
            'message' => 'Token renovado correctamente.',
            'data' => $result,
        ]);
    }

    public function logout(LogoutRequest $request): JsonResponse
    {
        /** @var Request $httpRequest */
        $httpRequest = $request;

        $this->authService->logout(
            user: $httpRequest->user(),
            accessClaims: (array) $httpRequest->attributes->get('jwt_access_claims', []),
            accessToken: (string) $httpRequest->attributes->get('jwt_access_token', ''),
            refreshToken: $request->validated()['refresh_token'],
        );

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}