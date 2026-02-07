<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Shed;
use App\Repositories\ShedRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShedService
{
    public function __construct(
        protected ShedRepository $shedRepository
    ) {}

    /**
     * Get a paginated list of sheds for a farm with optional search.
     *
     * @return LengthAwarePaginator<int, Shed>
     */
    public function getPaginatedByFarm(int $farmId, ?string $search, int $perPage = 15): LengthAwarePaginator
    {
        return $this->shedRepository->getShedsPaginatedByFarm($farmId, $search ?: null, $perPage);
    }

    /**
     * Find a shed by id; throws if not found.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id): Shed
    {
        return $this->shedRepository->getShedById($id);
    }

    /**
     * Create a new shed for a farm.
     *
     * @param  array<string, mixed>  $data  Validated data (name, capacity, type).
     * @param  int|null  $userId  Authenticated user id for created_by.
     */
    public function create(int $farmId, array $data, ?int $userId = null): Shed
    {
        $data['farm_id'] = $farmId;
        if ($userId !== null) {
            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;
        }

        return $this->shedRepository->createShed($data);
    }

    /**
     * Update an existing shed.
     *
     * @param  array<string, mixed>  $data  Validated data.
     * @param  int|null  $userId  Authenticated user id for updated_by.
     */
    public function update(int $id, array $data, ?int $userId = null): ?Shed
    {
        if ($userId !== null) {
            $data['updated_by'] = $userId;
        }

        return $this->shedRepository->updateShed($id, $data);
    }

    /**
     * Soft-delete a shed by id.
     */
    public function delete(int $id): bool
    {
        return $this->shedRepository->deleteShed($id);
    }

    /**
     * Soft-delete multiple sheds by ids. Returns the number deleted.
     *
     * @param  array<int>  $ids
     */
    public function deleteMany(array $ids): int
    {
        return $this->shedRepository->deleteShedsByIds($ids);
    }
}
