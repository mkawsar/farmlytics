<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends AbstractRepository
{
    public function getModelClass(): string
    {
        return User::class;
    }

    public function getAllUsers(): object
    {
        return $this->getAll();
    }

    public function getUserById(int $id): object
    {
        return $this->getById($id);
    }

    public function createUser(array $data): object
    {
        return $this->create($data);
    }
}
