<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{
    protected $model;

    abstract public function getModelClass(): string;

    public function __construct()
    {
        $this->model = app($this->getModelClass());
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * create new record
     */
    public function create(array $fields): object
    {
        return $this->model->create($fields);
    }

    /**
     * create or update if exist
     */
    public function createOrUpdate(array $fields, array $conditions): object
    {
        return $this->model->updateOrCreate($conditions, $fields);
    }

    /**
     * update record
     */
    public function update(int|string $id, array $fields): ?object
    {
        $record = $this->model->find($id);

        if ($record) {
            $record->update($fields);

            return $record;
        }

        return null;
    }

    /**
     * delete record
     */
    public function delete(array $conditions): bool
    {
        return $this->model->where($conditions)->delete();
    }

    /**
     * get record by id
     */
    public function getById(int $id): object
    {
        return $this->model->findOrFail($id);
    }

    /**
     * get record by conditions
     */
    public function getByConditions(array $conditions): object
    {
        return $this->model->where($conditions)->get();
    }

    /**
     * get all records
     */
    public function getAll(array $conditions = []): object
    {
        return $this->model->where($conditions)->get();
    }

    /**
     * get all records with pagination and conditions
     */
    public function getAllWithPagination(array $conditions, int $perPage = 10): object
    {
        return $this->model->where($conditions)->paginate($perPage);
    }

    /**
     * get first record by conditions
     */
    public function getFirst(array $conditions): object
    {
        return $this->model->where($conditions)->first();
    }
}
