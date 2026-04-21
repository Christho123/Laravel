<?php

namespace App\Services\Auth;

use App\Models\User\User;
use App\Repositories\Auth\AuthSessionRepository;
use App\Repositories\Auth\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AuthSessionRepository $authSessionRepository,
        private readonly JwtService $jwtService,
    ) {
    }

    public function register(array $data): array
    {
        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at?->toISOString(),
            'updated_at' => $user->updated_at?->toISOString(),
        ];
    }

    public function login(array $data): array
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son válidas.'],
            ]);
        }

        $tokens = $this->issueTokens($user);

        return $this->formatUserPayload($user, $tokens);
    }

    public function refresh(string $refreshToken): array
    {
        $refreshClaims = $this->jwtService->validateToken($refreshToken, 'refresh');

        $session = $this->authSessionRepository->findActiveSessionByRefreshToken(
            sessionId: (string) $refreshClaims['sid'],
            refreshJti: (string) $refreshClaims['jti'],
            refreshToken: $refreshToken,
        );

        if (!$session) {
            throw new AuthenticationException('El refresh token no es válido.');
        }

        $user = $this->userRepository->findById($session->user_id);

        if (!$user) {
            throw new AuthenticationException('Usuario no encontrado.');
        }

        $pair = $this->jwtService->issueTokenPair($user, $session->id);

        $this->authSessionRepository->rotateSessionTokens(
            session: $session,
            accessClaims: $pair['access_claims'],
            refreshClaims: $pair['refresh_claims'],
            accessToken: $pair['access_token'],
            refreshToken: $pair['refresh_token'],
        );

        return [
            'token_type' => 'Bearer',
            'access_token' => $pair['access_token'],
            'refresh_token' => $pair['refresh_token'],
            'refresh_expires_at' => $pair['refresh_claims']['exp'],
        ];
    }

    public function logout(User|null $user, array $accessClaims, string $accessToken, string $refreshToken): void
    {
        if (!$user) {
            throw new AuthenticationException('Usuario no autenticado.');
        }

        $refreshClaims = $this->jwtService->validateToken($refreshToken, 'refresh');

        if ((int) $refreshClaims['sub'] !== (int) $user->getKey()) {
            throw new AuthenticationException('El refresh token no pertenece al usuario autenticado.');
        }

        $session = $this->authSessionRepository->findActiveSessionByRefreshToken(
            sessionId: (string) $refreshClaims['sid'],
            refreshJti: (string) $refreshClaims['jti'],
            refreshToken: $refreshToken,
        );

        if (
            !$session
            || $session->access_jti !== ($accessClaims['jti'] ?? null)
            || $session->access_token_hash !== hash('sha256', $accessToken)
            || $session->user_id !== $user->getKey()
        ) {
            throw new AuthenticationException('La sesión no es válida para cerrar sesión.');
        }

        $this->authSessionRepository->revokeAccessToken($user, $session, $accessClaims, $accessToken);
        $this->authSessionRepository->revokeSession($session);
    }

    private function issueTokens(User $user): array
    {
        $sessionId = (string) Str::uuid();
        $pair = $this->jwtService->issueTokenPair($user, $sessionId);

        $this->authSessionRepository->createSession(
            user: $user,
            sessionId: $sessionId,
            accessClaims: $pair['access_claims'],
            refreshClaims: $pair['refresh_claims'],
            accessToken: $pair['access_token'],
            refreshToken: $pair['refresh_token'],
        );

        return [
            'token_type' => 'Bearer',
            'access_token' => $pair['access_token'],
            'refresh_token' => $pair['refresh_token'],
        ];
    }

    private function formatUserPayload(User $user, array $tokens): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'token_type' => $tokens['token_type'],
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
        ];
    }
}
