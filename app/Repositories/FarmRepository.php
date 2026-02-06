<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Farm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository for farm entity data access.
 *
 * @extends AbstractRepository<Farm>
 */
class FarmRepository extends AbstractRepository
{
    /**
     * @return class-string<Farm>
     */
    public function getModelClass(): string
    {
        return Farm::class;
    }

    /**
     * Get all farms, optionally filtered by conditions.
     *
     * @param  array<string, mixed>  $conditions
     * @return Collection<int, Farm>
     */
    public function getAllFarms(array $conditions = []): Collection
    {
        return $this->getAll($conditions);
    }

    /**
     * Get a paginated list of farms.
     *
     * @param  array<string, mixed>  $conditions
     * @return LengthAwarePaginator<int, Farm>
     */
    public function getAllFarmsPaginated(array $conditions = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->getAllWithPagination($conditions, $perPage);
    }

    /**
     * Get a paginated list of farms with optional search on name and location.
     *
     * @return LengthAwarePaginator<int, Farm>
     */
    public function getFarmsPaginatedWithSearch(?string $search, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if ($search !== null && trim($search) !== '') {
            $term = '%'.trim($search).'%';
            $query->where(function ($q) use ($term): void {
                $q->where('name', 'like', $term)
                    ->orWhere('location', 'like', $term);
            });
        }

        /** @var LengthAwarePaginator<int, Farm> */
        return $query->paginate($perPage);
    }

    /**
     * Find a farm by primary key; throws if not found.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getFarmById(int $id): Farm
    {
        /** @var Farm */
        return $this->getById($id);
    }

    /**
     * Create a new farm from the given attributes.
     *
     * @param  array<string, mixed>  $data
     */
    public function createFarm(array $data): Farm
    {
        /** @var Farm */
        return $this->create($data);
    }

    /**
     * Update a farm by primary key.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateFarm(int $id, array $data): ?Farm
    {
        $farm = $this->update($id, $data);

        return $farm instanceof Farm ? $farm : null;
    }

    /**
     * Soft-delete a farm by primary key. Returns true if found and deleted.
     */
    public function deleteFarm(int $id): bool
    {
        $farm = $this->model->newQuery()->find($id);

        if ($farm === null) {
            return false;
        }

        return $farm->delete();
    }

    /**
     * Soft-delete multiple farms by ids in a single query. Returns the number of deleted records.
     *
     * @param  array<int>  $ids
     */
    public function deleteFarmsByIds(array $ids): int
    {
        if ($ids === []) {
            return 0;
        }

        $ids = array_map('intval', array_unique($ids));

        return $this->model->newQuery()->whereIn('id', $ids)->delete();
    }
}
