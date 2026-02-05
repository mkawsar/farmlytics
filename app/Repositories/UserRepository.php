<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository for user entity data access.
 *
 * @extends AbstractRepository<User>
 */
class UserRepository extends AbstractRepository
{
    /**
     * @return class-string<User>
     */
    public function getModelClass(): string
    {
        return User::class;
    }

    /**
     * Get all users, optionally filtered by conditions.
     *
     * @param  array<string, mixed>  $conditions
     * @return Collection<int, User>
     */
    public function getAllUsers(array $conditions = []): Collection
    {
        return $this->getAll($conditions);
    }

    /**
     * Find a user by primary key; throws if not found.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getUserById(int $id): User
    {
        /** @var User */
        return $this->getById($id);
    }

    /**
     * Create a new user from the given attributes.
     *
     * @param  array<string, mixed>  $data
     */
    public function createUser(array $data): User
    {
        /** @var User */
        return $this->create($data);
    }
}
