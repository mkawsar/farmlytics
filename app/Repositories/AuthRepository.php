<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;

/**
 * Repository for authentication-related user lookups.
 *
 * @extends AbstractRepository<User>
 */
class AuthRepository extends AbstractRepository
{
    /**
     * @return class-string<User>
     */
    public function getModelClass(): string
    {
        return User::class;
    }

    /**
     * Find a user by email address.
     */
    public function findByEmail(string $email): ?User
    {
        $user = $this->getFirst(['email' => $email]);

        return $user instanceof User ? $user : null;
    }
}
