<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Animal;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Repository for animal entity data access.
 *
 * @extends AbstractRepository<Animal>
 */
class AnimalRepository extends AbstractRepository
{
    /**
     * @return class-string<Animal>
     */
    public function getModelClass(): string
    {
        return Animal::class;
    }

    /**
     * Get a paginated list of all animals with optional search on animal_id, breed.
     *
     * @return LengthAwarePaginator<int, Animal>
     */
    public function getAnimalsPaginated(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['shed', 'farm']);

        if ($search !== null && trim($search) !== '') {
            $term = '%'.trim($search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('animal_id', 'like', $term)
                    ->orWhere('breed', 'like', $term);
            });
        }

        /** @var LengthAwarePaginator<int, Animal> */
        return $query->orderBy('animal_id')->paginate($perPage);
    }

    /**
     * Get a paginated list of animals for a shed with optional search on animal_id, breed.
     *
     * @return LengthAwarePaginator<int, Animal>
     */
    public function getAnimalsPaginatedByShed(int $shedId, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['shed', 'farm'])->where('shed_id', $shedId);

        if ($search !== null && trim($search) !== '') {
            $term = '%'.trim($search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('animal_id', 'like', $term)
                    ->orWhere('breed', 'like', $term);
            });
        }

        /** @var LengthAwarePaginator<int, Animal> */
        return $query->orderBy('animal_id')->paginate($perPage);
    }

    /**
     * Find an animal by primary key; throws if not found.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getAnimalById(int $id): Animal
    {
        /** @var Animal */
        return $this->getById($id);
    }

    /**
     * Create a new animal from the given attributes.
     *
     * @param  array<string, mixed>  $data
     */
    public function createAnimal(array $data): Animal
    {
        /** @var Animal */
        return $this->create($data);
    }

    /**
     * Update an animal by primary key.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateAnimal(int $id, array $data): ?Animal
    {
        $animal = $this->update($id, $data);

        return $animal instanceof Animal ? $animal : null;
    }

    /**
     * Soft-delete an animal by primary key. Returns true if found and deleted.
     */
    public function deleteAnimal(int $id): bool
    {
        $animal = $this->model->newQuery()->find($id);

        if ($animal === null) {
            return false;
        }

        return $animal->delete();
    }

    /**
     * Soft-delete multiple animals by ids. Returns the number of deleted records.
     *
     * @param  array<int>  $ids
     */
    public function deleteAnimalsByIds(array $ids): int
    {
        if ($ids === []) {
            return 0;
        }

        $ids = array_map('intval', array_unique($ids));

        return $this->model->newQuery()->whereIn('id', $ids)->delete();
    }

    /** Sum purchase_price of animals purchased on a given date. */
    public function getTotalPurchasePriceForDate(Carbon $date): float
    {
        return (float) $this->model->newQuery()
            ->whereNotNull('purchase_price')
            ->whereDate('purchase_date', $date)
            ->sum('purchase_price');
    }

    /** Sum purchase_price of animals purchased in a given month. */
    public function getTotalPurchasePriceForMonth(Carbon $monthStart): float
    {
        $start = $monthStart->copy()->startOfMonth();
        $end = $monthStart->copy()->endOfMonth();

        return (float) $this->model->newQuery()
            ->whereNotNull('purchase_price')
            ->whereDate('purchase_date', '>=', $start)
            ->whereDate('purchase_date', '<=', $end)
            ->sum('purchase_price');
    }

    /** Daily purchase_price sums for a calendar month (by purchase_date). Returns map of date (Y-m-d) => amount. */
    public function getDailyPurchasePriceSumsForMonth(Carbon $monthStart): array
    {
        $start = $monthStart->copy()->startOfMonth();
        $end = $monthStart->copy()->endOfMonth();
        $rows = DB::table($this->model->getTable())
            ->selectRaw('DATE(purchase_date) as date, SUM(purchase_price) as total')
            ->whereNotNull('purchase_price')
            ->whereDate('purchase_date', '>=', $start)
            ->whereDate('purchase_date', '<=', $end)
            ->groupBy('date')
            ->get();
        $out = [];
        foreach ($rows as $row) {
            $out[$row->date] = (float) $row->total;
        }

        return $out;
    }

    /** Sum all purchase_price (all time). */
    public function getTotalPurchasePriceAll(): float
    {
        return (float) $this->model->newQuery()
            ->whereNotNull('purchase_price')
            ->sum('purchase_price');
    }
}
