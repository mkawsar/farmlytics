<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\MilkRecord;
use Carbon\Carbon;

/**
 * @extends AbstractRepository<MilkRecord>
 */
class MilkRecordRepository extends AbstractRepository
{
    public function getModelClass(): string
    {
        return MilkRecord::class;
    }

    public function getRecordById(int $id): MilkRecord
    {
        /** @var MilkRecord */
        return $this->getById($id);
    }

    /** @param  array<string, mixed>  $data */
    public function createRecord(array $data): MilkRecord
    {
        /** @var MilkRecord */
        return $this->create($data);
    }

    /** @param  array<string, mixed>  $data */
    public function updateRecord(int $id, array $data): ?MilkRecord
    {
        $model = $this->update($id, $data);

        return $model instanceof MilkRecord ? $model : null;
    }

    /** Total milk (liters) for an animal between dates. */
    public function getTotalLitersForAnimalBetweenDates(int $animalId, Carbon $start, Carbon $end): float
    {
        return (float) $this->model->newQuery()
            ->where('animal_id', $animalId)
            ->whereDate('record_date', '>=', $start)
            ->whereDate('record_date', '<=', $end)
            ->sum('quantity_liter');
    }

    /** Total milk (liters) for a shed between dates (for cost per liter). */
    public function getTotalLitersForShedBetweenDates(int $shedId, Carbon $start, Carbon $end): float
    {
        return (float) $this->model->newQuery()
            ->where('shed_id', $shedId)
            ->whereDate('record_date', '>=', $start)
            ->whereDate('record_date', '<=', $end)
            ->sum('quantity_liter');
    }

    /** Get paginated milk records for an animal. */
    public function getPaginatedByAnimal(int $animalId, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('animal_id', $animalId)
            ->orderBy('record_date', 'desc')
            ->paginate($perPage);
    }

    public function deleteRecord(int $id): bool
    {
        $record = $this->model->newQuery()->find($id);

        if ($record === null) {
            return false;
        }

        return $record->delete();
    }
}
