<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\IncomeType;
use App\Models\IncomeTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @extends AbstractRepository<IncomeTransaction>
 */
class IncomeTransactionRepository extends AbstractRepository
{
    public function getModelClass(): string
    {
        return IncomeTransaction::class;
    }

    public function getTransactionById(int $id): IncomeTransaction
    {
        /** @var IncomeTransaction */
        return $this->getById($id);
    }

    /** @param  array<string, mixed>  $data */
    public function createTransaction(array $data): IncomeTransaction
    {
        /** @var IncomeTransaction */
        return $this->create($data);
    }

    /** @param  array<string, mixed>  $data */
    public function updateTransaction(int $id, array $data): ?IncomeTransaction
    {
        $model = $this->update($id, $data);

        return $model instanceof IncomeTransaction ? $model : null;
    }

    /** Get income for an animal between dates (inclusive). */
    public function getForAnimalBetweenDates(int $animalId, Carbon $start, Carbon $end): Collection
    {
        /** @var Collection<int, IncomeTransaction> */
        return $this->model->newQuery()
            ->where('animal_id', $animalId)
            ->whereDate('transaction_date', '>=', $start)
            ->whereDate('transaction_date', '<=', $end)
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /** Sum total income amount for an animal between dates. */
    public function getTotalAmountForAnimalBetweenDates(int $animalId, Carbon $start, Carbon $end): float
    {
        return (float) $this->model->newQuery()
            ->where('animal_id', $animalId)
            ->whereDate('transaction_date', '>=', $start)
            ->whereDate('transaction_date', '<=', $end)
            ->sum('amount');
    }

    /** Sum animal sale income for an animal (all time; selling price when cow is sold). */
    public function getTotalAnimalSaleForAnimal(int $animalId): float
    {
        return (float) $this->model->newQuery()
            ->where('animal_id', $animalId)
            ->where('income_type', IncomeType::ANIMAL_SALE->value)
            ->sum('amount');
    }

    /** Sum dung sale income for a shed between dates (shed-level or per-animal; used to allocate per cow). */
    public function getTotalDungSaleForShedBetweenDates(int $shedId, Carbon $start, Carbon $end): float
    {
        return (float) $this->model->newQuery()
            ->where('shed_id', $shedId)
            ->where('income_type', IncomeType::DUNG_SALE->value)
            ->whereDate('transaction_date', '>=', $start)
            ->whereDate('transaction_date', '<=', $end)
            ->sum('amount');
    }

    /** Get paginated income for an animal. */
    public function getPaginatedByAnimal(int $animalId, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('animal_id', $animalId)
            ->orderBy('transaction_date', 'desc')
            ->paginate($perPage);
    }

    /** Get paginated list of all income transactions (for global income index). */
    public function getPaginatedAll(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['animal:id,animal_id,breed', 'shed:id,name', 'farm:id,name'])
            ->orderBy('transaction_date', 'desc')
            ->paginate($perPage);
    }

    /** Sum total income for a single day. */
    public function getTotalForDate(Carbon $date): float
    {
        return (float) $this->model->newQuery()
            ->whereDate('transaction_date', $date)
            ->sum('amount');
    }

    /** Sum total income for a calendar month (start to end of month). */
    public function getTotalForMonth(Carbon $monthStart): float
    {
        $start = $monthStart->copy()->startOfMonth();
        $end = $monthStart->copy()->endOfMonth();

        return (float) $this->model->newQuery()
            ->whereDate('transaction_date', '>=', $start)
            ->whereDate('transaction_date', '<=', $end)
            ->sum('amount');
    }

    /** Sum total income (all time). */
    public function getTotalAll(): float
    {
        return (float) $this->model->newQuery()->sum('amount');
    }

    /** Daily income sums for a calendar month. Returns map of date (Y-m-d) => amount. */
    public function getDailySumsForMonth(Carbon $monthStart): array
    {
        $start = $monthStart->copy()->startOfMonth();
        $end = $monthStart->copy()->endOfMonth();
        $rows = DB::table($this->model->getTable())
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->whereDate('transaction_date', '>=', $start)
            ->whereDate('transaction_date', '<=', $end)
            ->groupBy('date')
            ->get();
        $out = [];
        foreach ($rows as $row) {
            $out[$row->date] = (float) $row->total;
        }

        return $out;
    }

    public function deleteTransaction(int $id): bool
    {
        $record = $this->model->newQuery()->find($id);

        if ($record === null) {
            return false;
        }

        return $record->delete();
    }
}
