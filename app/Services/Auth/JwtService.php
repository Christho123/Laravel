<?php

namespace App\Services\Auth;

use App\Models\User\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class JwtService
{
    public function issueTokenPair(User $user, string $sessionId): array
    {
        $now = now();

        $accessClaims = $this->buildClaims($user, $sessionId, 'access', $now);
        $refreshClaims = $this->buildClaims($user, $sessionId, 'refresh', $now, config('jwt.refresh_ttl_days'), true);

        return [
            'access_token' => $this->encode($accessClaims),
            'refresh_token' => $this->encode($refreshClaims),
            'access_claims' => $accessClaims,
            'refresh_claims' => $refreshClaims,
        ];
    }

    public function validateToken(string $token, string $expectedType): array
    {
        $claims = $this->decode($token);

        if (($claims['typ'] ?? null) !== $expectedType) {
            throw new \RuntimeException('JWT type mismatch.');
        }

        if (array_key_exists('exp', $claims) && (int) $claims['exp'] < now()->timestamp) {
            throw new \RuntimeException('JWT expired.');
        }

        return $claims;
    }

    public function decode(string $token): array
    {
        [$header, $payload, $signature] = $this->splitToken($token);

        $expected = $this->base64UrlEncode(hash_hmac('sha256', $header.'.'.$payload, $this->secret(), true));

        if (!hash_equals($expected, $signature)) {
            throw new \RuntimeException('Invalid JWT signature.');
        }

        $claims = json_decode($this->base64UrlDecode($payload), true, 512, JSON_THROW_ON_ERROR);

        return is_array($claims) ? $claims : [];
    }

    private function buildClaims(
        User $user,
        string $sessionId,
        string $type,
        Carbon $now,
        int $ttl = 0,
        bool $days = false,
    ): array {
        $claims = [
            'iss' => config('jwt.issuer'),
            'sub' => $user->getKey(),
            'sid' => $sessionId,
            'jti' => (string) Str::uuid(),
            'typ' => $type,
            'email' => $user->email,
            'name' => $user->name,
            'iat' => $now->timestamp,
        ];

        if ($ttl > 0) {
            $exp = $days
                ? $now->copy()->addDays($ttl)
                : $now->copy()->addMinutes($ttl);

            $claims['exp'] = $exp->timestamp;
        }

        return $claims;
    }

    private function encode(array $claims): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $encodedHeader = $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $encodedPayload = $this->base64UrlEncode(json_encode($claims, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $signature = $this->base64UrlEncode(hash_hmac('sha256', $encodedHeader.'.'.$encodedPayload, $this->secret(), true));

        return $encodedHeader.'.'.$encodedPayload.'.'.$signature;
    }

    private function splitToken(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new \RuntimeException('Invalid JWT format.');
        }

        return $parts;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $remainder = strlen($value) % 4;

        if ($remainder) {
            $value .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($value, '-_', '+/'), true) ?: '';
    }

    private function secret(): string
    {
        $secret = config('jwt.secret') ?: config('app.key');

        if (is_string($secret) && str_starts_with($secret, 'base64:')) {
            $secret = base64_decode(substr($secret, 7)) ?: $secret;
        }

        return (string) $secret;
    }
}
