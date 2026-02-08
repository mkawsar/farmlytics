<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ExpenseType;
use App\Models\ExpenseTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends AbstractRepository<ExpenseTransaction>
 */
class ExpenseTransactionRepository extends AbstractRepository
{
    public function getModelClass(): string
    {
        return ExpenseTransaction::class;
    }

    public function getTransactionById(int $id): ExpenseTransaction
    {
        /** @var ExpenseTransaction */
        return $this->getById($id);
    }

    /** @param  array<string, mixed>  $data */
    public function createTransaction(array $data): ExpenseTransaction
    {
        /** @var ExpenseTransaction */
        return $this->create($data);
    }

    /** @param  array<string, mixed>  $data */
    public function updateTransaction(int $id, array $data): ?ExpenseTransaction
    {
        $model = $this->update($id, $data);

        return $model instanceof ExpenseTransaction ? $model : null;
    }

    /** Get direct (non-allocatable) expenses for an animal between dates. */
    public function getDirectForAnimalBetweenDates(int $animalId, Carbon $start, Carbon $end): Collection
    {
        $allocatableTypes = array_map(fn (ExpenseType $e) => $e->value, [
            ExpenseType::LABOUR,
            ExpenseType::ELECTRICITY,
            ExpenseType::WATER,
        ]);

        /** @var Collection<int, ExpenseTransaction> */
        return $this->model->newQuery()
            ->where('animal_id', $animalId)
            ->whereNotIn('expense_type', $allocatableTypes)
            ->whereDate('transaction_date', '>=', $start)
            ->whereDate('transaction_date', '<=', $end)
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /** Sum direct expense amount for an animal between dates (includes COW_PURCHASE if recorded as expense). */
    public function getTotalDirectAmountForAnimalBetweenDates(int $animalId, Carbon $start, Carbon $end): float
    {
        $allocatableTypes = array_map(fn (ExpenseType $e) => $e->value, [
            ExpenseType::LABOUR,
            ExpenseType::ELECTRICITY,
            ExpenseType::WATER,
        ]);

        return (float) $this->model->newQuery()
            ->where('animal_id', $animalId)
            ->whereNotIn('expense_type', $allocatableTypes)
            ->whereDate('transaction_date', '>=', $start)
            ->whereDate('transaction_date', '<=', $end)
            ->sum('amount');
    }

    /** Sum COW_PURCHASE expense for an animal between dates (for display; used when purchase is stored as expense). */
    public function getTotalCowPurchaseForAnimalBetweenDates(int $animalId, Carbon $start, Carbon $end): float
    {
        return (float) $this->model->newQuery()
            ->where('animal_id', $animalId)
            ->where('expense_type', ExpenseType::COW_PURCHASE->value)
            ->whereDate('transaction_date', '>=', $start)
            ->whereDate('transaction_date', '<=', $end)
            ->sum('amount');
    }

    /** Sum feed expense (fodder, concentrate, medicine) for an animal between dates. */
    public function getTotalFeedForAnimalBetweenDates(int $animalId, Carbon $start, Carbon $end): float
    {
        return (float) $this->model->newQuery()
            ->where('animal_id', $animalId)
            ->whereIn('expense_type', [
                ExpenseType::FODDER->value,
                ExpenseType::CONCENTRATE->value,
                ExpenseType::MEDICINE->value,
            ])
            ->whereDate('transaction_date', '>=', $start)
            ->whereDate('transaction_date', '<=', $end)
            ->sum('amount');
    }

    /** Get monthly allocatable expenses (labour, electricity, water) for a shed in a given month. */
    public function getMonthlyAllocatableForShed(int $shedId, Carbon $monthStart): Collection
    {
        /** @var Collection<int, ExpenseTransaction> */
        return $this->model->newQuery()
            ->where('shed_id', $shedId)
            ->whereIn('expense_type', [ExpenseType::LABOUR->value, ExpenseType::ELECTRICITY->value, ExpenseType::WATER->value])
            ->whereDate('period_month', $monthStart)
            ->get();
    }

    /** Sum total labour for a shed in a month (all labour: shed-level and per-animal). Divided by cow count for per-cow cost. */
    public function getTotalMonthlyLabourForShed(int $shedId, Carbon $monthStart): float
    {
        $monthEnd = $monthStart->copy()->endOfMonth();

        return (float) $this->model->newQuery()
            ->where('shed_id', $shedId)
            ->where('expense_type', ExpenseType::LABOUR->value)
            ->where(function ($q) use ($monthStart, $monthEnd) {
                $q->whereDate('period_month', $monthStart)
                    ->orWhere(function ($q2) use ($monthStart, $monthEnd) {
                        $q2->whereNull('period_month')
                            ->whereDate('transaction_date', '>=', $monthStart)
                            ->whereDate('transaction_date', '<=', $monthEnd);
                    });
            })
            ->sum('amount');
    }

    /** Sum total allocatable (electricity, water) for a shed in a month (shed-level only: animal_id null). */
    public function getTotalMonthlyNonLabourAllocatableForShed(int $shedId, Carbon $monthStart): float
    {
        $monthEnd = $monthStart->copy()->endOfMonth();

        return (float) $this->model->newQuery()
            ->where('shed_id', $shedId)
            ->whereNull('animal_id')
            ->whereIn('expense_type', [ExpenseType::ELECTRICITY->value, ExpenseType::WATER->value])
            ->where(function ($q) use ($monthStart, $monthEnd) {
                $q->whereDate('period_month', $monthStart)
                    ->orWhere(function ($q2) use ($monthStart, $monthEnd) {
                        $q2->whereNull('period_month')
                            ->whereDate('transaction_date', '>=', $monthStart)
                            ->whereDate('transaction_date', '<=', $monthEnd);
                    });
            })
            ->sum('amount');
    }

    /** Sum total allocatable amount for a shed in a month (shed-level only: animal_id null). Includes period_month set or period_month null with transaction_date in that month. */
    public function getTotalMonthlyAllocatableForShed(int $shedId, Carbon $monthStart): float
    {
        return $this->getTotalMonthlyLabourForShed($shedId, $monthStart)
            + $this->getTotalMonthlyNonLabourAllocatableForShed($shedId, $monthStart);
    }

    /** Get paginated expense for an animal. */
    public function getPaginatedByAnimal(int $animalId, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('animal_id', $animalId)
            ->orderBy('transaction_date', 'desc')
            ->paginate($perPage);
    }

    /** Get paginated list of all expense transactions (for global expense index). */
    public function getPaginatedAll(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['animal:id,animal_id,breed', 'shed:id,name', 'farm:id,name'])
            ->orderBy('transaction_date', 'desc')
            ->paginate($perPage);
    }

    /** Sum total expense for a single day (direct expenses with transaction_date = date). */
    public function getTotalForDate(Carbon $date): float
    {
        return (float) $this->model->newQuery()
            ->whereDate('transaction_date', $date)
            ->sum('amount');
    }

    /** Daily expense sums for a calendar month (transaction_date; direct only). Returns map of date (Y-m-d) => amount. */
    public function getDailySumsForMonth(Carbon $monthStart): array
    {
        $start = $monthStart->copy()->startOfMonth();
        $end = $monthStart->copy()->endOfMonth();
        $rows = $this->model->newQuery()
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

    /** Daily COW_PURCHASE expense sums for a calendar month. Returns map of date (Y-m-d) => amount. */
    public function getDailyCowPurchaseSumsForMonth(Carbon $monthStart): array
    {
        $start = $monthStart->copy()->startOfMonth();
        $end = $monthStart->copy()->endOfMonth();
        $rows = $this->model->newQuery()
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->where('expense_type', ExpenseType::COW_PURCHASE->value)
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

    /** Sum COW_PURCHASE expense for a single day (to avoid double-count with animal purchase in dashboard). */
    public function getTotalCowPurchaseForDate(Carbon $date): float
    {
        return (float) $this->model->newQuery()
            ->where('expense_type', ExpenseType::COW_PURCHASE->value)
            ->whereDate('transaction_date', $date)
            ->sum('amount');
    }

    /** Sum COW_PURCHASE expense for a month. */
    public function getTotalCowPurchaseForMonth(Carbon $monthStart): float
    {
        $start = $monthStart->copy()->startOfMonth();
        $end = $monthStart->copy()->endOfMonth();

        return (float) $this->model->newQuery()
            ->where('expense_type', ExpenseType::COW_PURCHASE->value)
            ->whereDate('transaction_date', '>=', $start)
            ->whereDate('transaction_date', '<=', $end)
            ->sum('amount');
    }

    /** Sum all COW_PURCHASE expense. */
    public function getTotalCowPurchaseAll(): float
    {
        return (float) $this->model->newQuery()
            ->where('expense_type', ExpenseType::COW_PURCHASE->value)
            ->sum('amount');
    }

    /**
     * Sum total expense for a calendar month: direct expenses in the month + allocatable (labour, electricity, water) for that month.
     */
    public function getTotalForMonth(Carbon $monthStart): float
    {
        $start = $monthStart->copy()->startOfMonth();
        $end = $monthStart->copy()->endOfMonth();

        $direct = (float) $this->model->newQuery()
            ->whereDate('transaction_date', '>=', $start)
            ->whereDate('transaction_date', '<=', $end)
            ->sum('amount');

        $allocatable = (float) $this->model->newQuery()
            ->whereIn('expense_type', [ExpenseType::LABOUR->value, ExpenseType::ELECTRICITY->value, ExpenseType::WATER->value])
            ->whereDate('period_month', $monthStart)
            ->sum('amount');

        return $direct + $allocatable;
    }

    /** Sum total expense (all time). */
    public function getTotalAll(): float
    {
        return (float) $this->model->newQuery()->sum('amount');
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
