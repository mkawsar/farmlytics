<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Shed;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository for shed entity data access.
 *
 * @extends AbstractRepository<Shed>
 */
class ShedRepository extends AbstractRepository
{
    /**
     * @return class-string<Shed>
     */
    public function getModelClass(): string
    {
        return Shed::class;
    }

    /**
     * Get all sheds for a farm.
     *
     * @return Collection<int, Shed>
     */
    public function getShedsByFarmId(int $farmId): Collection
    {
        return $this->getByConditions(['farm_id' => $farmId]);
    }

    /**
     * Get a paginated list of sheds for a farm with optional search on name.
     *
     * @return LengthAwarePaginator<int, Shed>
     */
    public function getShedsPaginatedByFarm(int $farmId, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('farm')->where('farm_id', $farmId);

        if ($search !== null && trim($search) !== '') {
            $term = '%'.trim($search).'%';
            $query->where('name', 'like', $term);
        }

        /** @var LengthAwarePaginator<int, Shed> */
        return $query->paginate($perPage);
    }

    /**
     * Find a shed by primary key; throws if not found.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getShedById(int $id): Shed
    {
        /** @var Shed */
        return $this->getById($id);
    }

    /**
     * Create a new shed from the given attributes.
     *
     * @param  array<string, mixed>  $data
     */
    public function createShed(array $data): Shed
    {
        /** @var Shed */
        return $this->create($data);
    }

    /**
     * Update a shed by primary key.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateShed(int $id, array $data): ?Shed
    {
        $shed = $this->update($id, $data);

        return $shed instanceof Shed ? $shed : null;
    }

    /**
     * Soft-delete a shed by primary key. Returns true if found and deleted.
     */
    public function deleteShed(int $id): bool
    {
        $shed = $this->model->newQuery()->find($id);

        if ($shed === null) {
            return false;
        }

        return $shed->delete();
    }

    /**
     * Soft-delete multiple sheds by ids. Returns the number of deleted records.
     *
     * @param  array<int>  $ids
     */
    public function deleteShedsByIds(array $ids): int
    {
        if ($ids === []) {
            return 0;
        }

        $ids = array_map('intval', array_unique($ids));

        return $this->model->newQuery()->whereIn('id', $ids)->delete();
    }
}
