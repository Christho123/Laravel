<?php

namespace App\Repositories\Auth;

use App\Models\User\User;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function findById(int|string $id): ?User
    {
        return User::query()->find($id);
    }

    public function create(array $data): User
    {
        return User::query()->create($data);
    }
}