<?php

namespace App\Repositories\Auth;

use App\Models\Auth\AuthSession;
use App\Models\Auth\RevokedAccessToken;
use App\Models\User\User;
use Illuminate\Support\Carbon;

class AuthSessionRepository
{
    public function createSession(
        User $user,
        string $sessionId,
        array $accessClaims,
        array $refreshClaims,
        string $accessToken,
        string $refreshToken,
    ): AuthSession {
        return AuthSession::query()->create([
            'id' => $sessionId,
            'user_id' => $user->getKey(),
            'access_jti' => $accessClaims['jti'],
            'refresh_jti' => $refreshClaims['jti'],
            'access_token_hash' => hash('sha256', $accessToken),
            'refresh_token_hash' => hash('sha256', $refreshToken),
            'access_expires_at' => Carbon::createFromTimestamp($accessClaims['exp']),
            'refresh_expires_at' => Carbon::createFromTimestamp($refreshClaims['exp']),
        ]);
    }

    public function rotateSessionTokens(
        AuthSession $session,
        array $accessClaims,
        array $refreshClaims,
        string $accessToken,
        string $refreshToken,
    ): AuthSession {
        $session->forceFill([
            'access_jti' => $accessClaims['jti'],
            'refresh_jti' => $refreshClaims['jti'],
            'access_token_hash' => hash('sha256', $accessToken),
            'refresh_token_hash' => hash('sha256', $refreshToken),
            'access_expires_at' => Carbon::createFromTimestamp($accessClaims['exp']),
            'refresh_expires_at' => Carbon::createFromTimestamp($refreshClaims['exp']),
            'revoked_at' => null,
        ])->save();

        return $session->refresh();
    }

    public function findActiveSessionByUuid(string $sessionId): ?AuthSession
    {
        return AuthSession::query()
            ->whereKey($sessionId)
            ->whereNull('revoked_at')
            ->first();
    }

    public function findActiveSessionByRefreshToken(string $sessionId, string $refreshJti, string $refreshToken): ?AuthSession
    {
        return AuthSession::query()
            ->whereKey($sessionId)
            ->where('refresh_jti', $refreshJti)
            ->where('refresh_token_hash', hash('sha256', $refreshToken))
            ->whereNull('revoked_at')
            ->first();
    }

    public function revokeSession(AuthSession $session): void
    {
        $session->forceFill(['revoked_at' => now()])->save();
    }

    public function revokeAccessToken(
        User $user,
        AuthSession $session,
        array $accessClaims,
        string $accessToken,
    ): RevokedAccessToken {
        return RevokedAccessToken::query()->create([
            'user_id' => $user->getKey(),
            'session_id' => $session->getKey(),
            'jti' => $accessClaims['jti'],
            'token_hash' => hash('sha256', $accessToken),
            'expires_at' => Carbon::createFromTimestamp($accessClaims['exp']),
            'revoked_at' => now(),
        ]);
    }

    public function isAccessTokenRevoked(string $jti): bool
    {
        return RevokedAccessToken::query()
            ->where('jti', $jti)
            ->exists();
    }
}