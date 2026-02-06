<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Farm;
use App\Repositories\FarmRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FarmService
{
    public function __construct(
        protected FarmRepository $farmRepository
    ) {}

    /**
     * Get a paginated list of farms.
     *
     * @param  array<string, mixed>  $conditions
     */
    public function getAllPaginated(array $conditions = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->farmRepository->getAllFarmsPaginated($conditions, $perPage);
    }

    /**
     * Find a farm by id; throws if not found.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id): Farm
    {
        return $this->farmRepository->getFarmById($id);
    }

    /**
     * Create a new farm.
     *
     * @param  array<string, mixed>  $data  Validated data (name, location, capacity, type).
     * @param  int|null  $userId  Authenticated user id for created_by.
     */
    public function create(array $data, ?int $userId = null): Farm
    {
        if ($userId !== null) {
            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;
        }

        return $this->farmRepository->createFarm($data);
    }

    /**
     * Update an existing farm.
     *
     * @param  array<string, mixed>  $data  Validated data.
     * @param  int|null  $userId  Authenticated user id for updated_by.
     */
    public function update(int $id, array $data, ?int $userId = null): ?Farm
    {
        if ($userId !== null) {
            $data['updated_by'] = $userId;
        }

        return $this->farmRepository->updateFarm($id, $data);
    }

    /**
     * Soft-delete a farm by id.
     */
    public function delete(int $id): bool
    {
        return $this->farmRepository->deleteFarm($id);
    }
}
