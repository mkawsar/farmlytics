<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MilkRecord;
use App\Repositories\MilkRecordRepository;

class MilkRecordService
{
    public function __construct(
        protected MilkRecordRepository $milkRecordRepository,
        protected AnimalService $animalService
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createForAnimal(int $animalId, array $data, ?int $userId = null): MilkRecord
    {
        $animal = $this->animalService->getById($animalId);
        $data['animal_id'] = $animalId;
        $data['shed_id'] = $animal->shed_id;
        $data['farm_id'] = $animal->farm_id;
        if ($userId !== null) {
            $data['created_by'] = $userId;
        }

        return $this->milkRecordRepository->createRecord($data);
    }

    public function getById(int $id): MilkRecord
    {
        return $this->milkRecordRepository->getRecordById($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): ?MilkRecord
    {
        return $this->milkRecordRepository->updateRecord($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->milkRecordRepository->deleteRecord($id);
    }

    /** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, MilkRecord> */
    public function getPaginatedByAnimal(int $animalId, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->milkRecordRepository->getPaginatedByAnimal($animalId, $perPage);
    }
}
