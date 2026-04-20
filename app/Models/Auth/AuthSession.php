<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AuthSession extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'auth_sessions';

    protected $fillable = [
        'id',
        'user_id',
        'access_jti',
        'refresh_jti',
        'access_token_hash',
        'refresh_token_hash',
        'access_expires_at',
        'refresh_expires_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'access_expires_at' => 'datetime',
            'refresh_expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }
}