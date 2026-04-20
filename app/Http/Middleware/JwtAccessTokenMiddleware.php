<?php

namespace App\Http\Middleware;

use App\Models\User\User;
use App\Repositories\Auth\AuthSessionRepository;
use App\Services\Auth\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAccessTokenMiddleware
{
    public function __construct(
        private readonly JwtService $jwtService,
        private readonly AuthSessionRepository $authSessionRepository,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token de acceso requerido.'], 401);
        }

        try {
            $claims = $this->jwtService->validateToken($token, 'access');
        } catch (\Throwable) {
            return response()->json(['message' => 'Token inválido o expirado.'], 401);
        }

        if ($this->authSessionRepository->isAccessTokenRevoked($claims['jti'])) {
            return response()->json(['message' => 'Token revocado.'], 401);
        }

        $session = $this->authSessionRepository->findActiveSessionByUuid($claims['sid']);

        if (
            !$session
            || $session->access_jti !== $claims['jti']
            || $session->access_token_hash !== hash('sha256', $token)
            || $session->isRevoked()
        ) {
            return response()->json(['message' => 'Sesión inválida o cerrada.'], 401);
        }

        $user = User::query()->find($claims['sub']);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado.'], 401);
        }

        $request->setUserResolver(static fn () => $user);
        $request->attributes->set('jwt_access_claims', $claims);
        $request->attributes->set('jwt_access_token', $token);
        $request->attributes->set('jwt_session_uuid', $session->id);

        return $next($request);
    }
}