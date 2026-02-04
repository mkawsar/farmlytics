<?php

namespace App\Repositories;

use App\Models\User;

class AuthRepository extends AbstractRepository
{
    public function getModelClass(): string
    {
        return User::class;
    }

    public function findByEmail(string $email): ?User
    {
        $result = $this->getFirst(['email' => $email]);

        return $result instanceof User ? $result : null;
    }
}
