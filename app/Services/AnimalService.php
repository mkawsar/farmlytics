<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Animal;
use App\Repositories\AnimalRepository;
use App\Support\BreedCode;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AnimalService
{
    public function __construct(
        protected AnimalRepository $animalRepository,
        protected ShedService $shedService
    ) {}

    /**
     * Get all active animals (for dashboard lifecycle report).
     *
     * @return Collection<int, Animal>
     */
    public function getActiveAnimals(): Collection
    {
        return $this->animalRepository->getActiveAnimals();
    }

    /**
     * Get a paginated list of all animals with optional search.
     *
     * @return LengthAwarePaginator<int, Animal>
     */
    public function getPaginated(?string $search, int $perPage = 15): LengthAwarePaginator
    {
        return $this->animalRepository->getAnimalsPaginated($search ?: null, $perPage);
    }

    /**
     * Get a paginated list of animals for a shed with optional search.
     *
     * @return LengthAwarePaginator<int, Animal>
     */
    public function getPaginatedByShed(int $shedId, ?string $search, int $perPage = 15): LengthAwarePaginator
    {
        return $this->animalRepository->getAnimalsPaginatedByShed($shedId, $search ?: null, $perPage);
    }

    /**
     * Find an animal by id; throws if not found.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id): Animal
    {
        return $this->animalRepository->getAnimalById($id);
    }

    /**
     * Generate animal_id in format CODE-YYYYMM-N (e.g. HF-202602-1).
     * CODE = breed code (e.g. Holstein → HF), YYYYMM = year-month, N = next sequence for that code+month.
     */
    public function generateAnimalId(string $breed, ?Carbon $date = null): string
    {
        $date = $date ?? Carbon::today();
        $code = BreedCode::codeFor($breed);
        $yearMonth = $date->format('Ym');
        $seq = $this->animalRepository->getNextSequenceForBreedCodeAndMonth($code, $yearMonth);

        return "{$code}-{$yearMonth}-{$seq}";
    }

    /**
     * Create a new animal for a shed.
     * If animal_id is empty, it is auto-generated as CODE-YYYYMM-N (e.g. HF-202602-1).
     *
     * @param  array<string, mixed>  $data  Validated data (animal_id, breed, gender, etc.).
     * @param  int|null  $userId  Authenticated user id for created_by.
     */
    public function create(int $shedId, array $data, ?int $userId = null): Animal
    {
        $shed = $this->shedService->getById($shedId);
        $data['shed_id'] = $shedId;
        $data['farm_id'] = $shed->farm_id;
        if ($userId !== null) {
            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;
        }
        // Always generate animal_id from breed (e.g. Holstein → HF-202602-1)
        $data['animal_id'] = $this->generateAnimalId($data['breed'] ?? '');

        return $this->animalRepository->createAnimal($data);
    }

    /**
     * Update an existing animal.
     *
     * @param  array<string, mixed>  $data  Validated data.
     * @param  int|null  $userId  Authenticated user id for updated_by.
     */
    public function update(int $id, array $data, ?int $userId = null): ?Animal
    {
        if ($userId !== null) {
            $data['updated_by'] = $userId;
        }

        return $this->animalRepository->updateAnimal($id, $data);
    }

    /**
     * Soft-delete an animal by id.
     */
    public function delete(int $id): bool
    {
        return $this->animalRepository->deleteAnimal($id);
    }

    /**
     * Soft-delete multiple animals by ids. Returns the number deleted.
     *
     * @param  array<int>  $ids
     */
    public function deleteMany(array $ids): int
    {
        return $this->animalRepository->deleteAnimalsByIds($ids);
    }

    /** Sum purchase_price of animals purchased on a given date. */
    public function getTotalPurchasePriceForDate(Carbon $date): float
    {
        return $this->animalRepository->getTotalPurchasePriceForDate($date);
    }

    /** Sum purchase_price of animals purchased in a given month. */
    public function getTotalPurchasePriceForMonth(Carbon $monthStart): float
    {
        return $this->animalRepository->getTotalPurchasePriceForMonth($monthStart);
    }

    /** Sum all purchase_price (all time). */
    public function getTotalPurchasePriceAll(): float
    {
        return $this->animalRepository->getTotalPurchasePriceAll();
    }

    /** Daily purchase_price sums for a calendar month (by purchase_date). Returns map of date (Y-m-d) => amount. */
    public function getDailyPurchasePriceSumsForMonth(Carbon $monthStart): array
    {
        return $this->animalRepository->getDailyPurchasePriceSumsForMonth($monthStart);
    }
}
