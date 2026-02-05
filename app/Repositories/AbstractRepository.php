<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Base repository for Eloquent models.
 *
 * Provides a consistent data access layer: create, read, update, delete (CRUD)
 * and common query helpers. Concrete repositories extend this class and
 * implement getModelClass() to bind to a specific model.
 *
 * @template T of Model
 */
abstract class AbstractRepository
{
    /** @var T */
    protected Model $model;

    /**
     * Return the fully qualified model class name (e.g. App\Models\User).
     *
     * @return class-string<T>
     */
    abstract public function getModelClass(): string;

    public function __construct()
    {
        $class = $this->getModelClass();
        $this->model = app($class);
    }

    /**
     * Get the underlying Eloquent model instance (new query each time).
     *
     * @return T
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    /**
     * Create a new record from the given attributes.
     *
     * @param  array<string, mixed>  $attributes
     * @return T
     */
    public function create(array $attributes): Model
    {
        return $this->model->newQuery()->create($attributes);
    }

    /**
     * Create a new record or update an existing one matching the conditions.
     *
     * @param  array<string, mixed>  $attributes  Attributes to set (on create and update).
     * @param  array<string, mixed>  $conditions  Attributes used to find existing record.
     * @return T
     */
    public function createOrUpdate(array $attributes, array $conditions): Model
    {
        return $this->model->newQuery()->updateOrCreate($conditions, $attributes);
    }

    // -------------------------------------------------------------------------
    // Read
    // -------------------------------------------------------------------------

    /**
     * Find a record by primary key; throws if not found.
     *
     * @return T
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int|string $id): Model
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    /**
     * Find the first record matching the given conditions.
     *
     * @param  array<string, mixed>  $conditions
     * @return T|null
     */
    public function getFirst(array $conditions): ?Model
    {
        $result = $this->model->newQuery()->where($conditions)->first();

        return $result instanceof Model ? $result : null;
    }

    /**
     * Get all records matching the given conditions.
     *
     * @param  array<string, mixed>  $conditions
     * @return Collection<int, T>
     */
    public function getByConditions(array $conditions): Collection
    {
        /** @var Collection<int, T> $result */
        $result = $this->model->newQuery()->where($conditions)->get();

        return $result;
    }

    /**
     * Get all records, optionally filtered by conditions.
     *
     * @param  array<string, mixed>  $conditions
     * @return Collection<int, T>
     */
    public function getAll(array $conditions = []): Collection
    {
        $query = $this->model->newQuery();

        if ($conditions !== []) {
            $query->where($conditions);
        }

        /** @var Collection<int, T> $result */
        $result = $query->get();

        return $result;
    }

    /**
     * Get a paginated list of records matching the given conditions.
     *
     * @param  array<string, mixed>  $conditions
     * @return LengthAwarePaginator<int, T>
     */
    public function getAllWithPagination(array $conditions = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if ($conditions !== []) {
            $query->where($conditions);
        }

        /** @var LengthAwarePaginator<int, T> $result */
        $result = $query->paginate($perPage);

        return $result;
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    /**
     * Update a record by primary key. Returns the updated model or null if not found.
     *
     * @param  array<string, mixed>  $attributes
     * @return T|null
     */
    public function update(int|string $id, array $attributes): ?Model
    {
        $record = $this->model->newQuery()->find($id);

        if ($record === null) {
            return null;
        }

        $record->update($attributes);

        return $record->fresh();
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    /**
     * Delete all records matching the given conditions.
     *
     * For soft-deletable models, this performs a soft delete unless forceDelete is used on the query.
     *
     * @param  array<string, mixed>  $conditions
     * @return int Number of rows deleted (or soft-deleted).
     */
    public function delete(array $conditions): int
    {
        return $this->model->newQuery()->where($conditions)->delete();
    }
}
