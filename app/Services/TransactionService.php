<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\IncomeType;
use App\Enums\Status;
use App\Models\Animal;
use App\Models\ExpenseTransaction;
use App\Models\IncomeTransaction;
use App\Repositories\ExpenseTransactionRepository;
use App\Repositories\IncomeTransactionRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransactionService
{
    public function __construct(
        protected IncomeTransactionRepository $incomeRepository,
        protected ExpenseTransactionRepository $expenseRepository,
        protected AnimalService $animalService
    ) {}

    /**
     * Create an income transaction for an animal.
     *
     * @param  array<string, mixed>  $data
     */
    public function createIncomeForAnimal(int $animalId, array $data, ?int $userId = null): IncomeTransaction
    {
        $animal = $this->animalService->getById($animalId);
        $data['animal_id'] = $animalId;
        $data['shed_id'] = $animal->shed_id;
        $data['farm_id'] = $animal->farm_id;
        if ($userId !== null) {
            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;
        }

        $transaction = $this->incomeRepository->createTransaction($data);

        if (($data['income_type'] ?? '') === IncomeType::ANIMAL_SALE->value) {
            $this->animalService->update($animalId, ['status' => Status::SOLD->value], $userId);
        }

        return $transaction;
    }

    public function getIncomeById(int $id): IncomeTransaction
    {
        return $this->incomeRepository->getTransactionById($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateIncome(int $id, array $data, ?int $userId = null): ?IncomeTransaction
    {
        if ($userId !== null) {
            $data['updated_by'] = $userId;
        }

        return $this->incomeRepository->updateTransaction($id, $data);
    }

    public function deleteIncome(int $id): bool
    {
        return $this->incomeRepository->deleteTransaction($id);
    }

    /**
     * Create an expense transaction. For allocatable types (labour, electricity, water), animal_id should be null and shed_id + period_month set.
     *
     * @param  array<string, mixed>  $data
     */
    public function createExpense(array $data, ?int $userId = null): ExpenseTransaction
    {
        if ($userId !== null) {
            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;
        }
        if (! empty($data['animal_id'])) {
            $animal = $this->animalService->getById((int) $data['animal_id']);
            $data['shed_id'] = $data['shed_id'] ?? $animal->shed_id;
            $data['farm_id'] = $data['farm_id'] ?? $animal->farm_id;
        }

        return $this->expenseRepository->createTransaction($data);
    }

    public function getExpenseById(int $id): ExpenseTransaction
    {
        return $this->expenseRepository->getTransactionById($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateExpense(int $id, array $data, ?int $userId = null): ?ExpenseTransaction
    {
        if ($userId !== null) {
            $data['updated_by'] = $userId;
        }

        return $this->expenseRepository->updateTransaction($id, $data);
    }

    public function deleteExpense(int $id): bool
    {
        return $this->expenseRepository->deleteTransaction($id);
    }

    /**
     * Get profit & loss for an animal over a date range with clear breakdown.
     * Formula: Profit = (Income + Dung allocated) − (Labour allocated + Feed + Purchase).
     *
     * @return array{income: float, dung_allocated: float, feed_expense: float, labour_allocated: float, purchase_expense: float, total_income: float, total_expense: float, profit_loss: float, total_direct_expense: float, allocated_expense: float}
     */
    public function getProfitLossForAnimal(int $animalId, Carbon $start, Carbon $end): array
    {
        $animal = $this->animalService->getById($animalId);
        $totalIncome = $this->incomeRepository->getTotalAmountForAnimalBetweenDates($animalId, $start, $end);
        $totalDirectExpense = $this->expenseRepository->getTotalDirectAmountForAnimalBetweenDates($animalId, $start, $end);
        $allocatedExpense = $this->getAllocatedExpenseForAnimal($animal, $start, $end);
        $labourAllocated = $allocatedExpense;
        $purchaseFromExpense = $this->expenseRepository->getTotalCowPurchaseForAnimalBetweenDates($animalId, $start, $end);
        $purchaseExpense = $purchaseFromExpense;
        $purchaseFromAnimal = 0.0;
        if ($purchaseExpense === 0.0 && $animal->purchase_price !== null) {
            $inRange = false;
            $isLifetimeRange = $start->format('Y-m-d') === $animal->created_at->copy()->startOfDay()->format('Y-m-d');
            if ($animal->purchase_date) {
                $purchaseDate = Carbon::parse($animal->purchase_date);
                $inRange = $purchaseDate->between($start, $end);
            } else {
                $inRange = $animal->created_at->between($start, $end);
            }
            if ($inRange || $isLifetimeRange) {
                $purchaseFromAnimal = (float) $animal->purchase_price;
                $purchaseExpense = $purchaseFromAnimal;
            }
        }
        $totalExpense = $totalDirectExpense + $labourAllocated + $purchaseFromAnimal;

        $dungAllocated = 0.0;
        if ($animal->shed_id) {
            $totalDungShed = $this->incomeRepository->getTotalDungSaleForShedBetweenDates($animal->shed_id, $start, $end);
            $cowCount = $this->getAnimalCountInShedAtMonth($animal->shed_id, $end->copy()->endOfMonth());
            if ($cowCount > 0) {
                $dungAllocated = $totalDungShed / $cowCount;
            }
        }
        $totalIncomeWithDung = $totalIncome + $dungAllocated;
        $profitLoss = $totalIncomeWithDung - $totalExpense;

        $feedExpense = $this->expenseRepository->getTotalFeedForAnimalBetweenDates($animalId, $start, $end);

        return [
            'income' => $totalIncome,
            'dung_allocated' => $dungAllocated,
            'feed_expense' => $feedExpense,
            'labour_allocated' => $labourAllocated,
            'purchase_expense' => $purchaseExpense,
            'total_income' => $totalIncomeWithDung,
            'total_expense' => $totalExpense,
            'profit_loss' => $profitLoss,
            'total_direct_expense' => $totalDirectExpense,
            'allocated_expense' => $allocatedExpense,
        ];
    }

    /**
     * Allocate monthly shed costs (labour, electricity, water) to this animal for each month in range.
     * Uses current animal count in shed per month (no movement history).
     */
    protected function getAllocatedExpenseForAnimal(Animal $animal, Carbon $start, Carbon $end): float
    {
        $shedId = $animal->shed_id;
        if (! $shedId) {
            return 0.0;
        }

        $total = 0.0;
        $cursor = $start->copy()->startOfMonth();
        while ($cursor->lte($end)) {
            $monthTotal = $this->expenseRepository->getTotalMonthlyAllocatableForShed($shedId, $cursor);
            if ($monthTotal > 0) {
                $animalCount = $this->getAnimalCountInShedAtMonth($shedId, $cursor);
                if ($animalCount > 0) {
                    $total += $monthTotal / $animalCount;
                }
            }
            $cursor->addMonth();
        }

        return $total;
    }

    /** Animal count in shed (current members; no movement history). */
    protected function getAnimalCountInShedAtMonth(int $shedId, Carbon $month): int
    {
        return Animal::query()
            ->where('shed_id', $shedId)
            ->where('created_at', '<=', $month->copy()->endOfMonth())
            ->where(function ($q) use ($month) {
                $q->whereNull('deleted_at')
                    ->orWhere('deleted_at', '>', $month->copy()->endOfMonth());
            })
            ->count();
    }

    /** @return LengthAwarePaginator<int, IncomeTransaction> */
    public function getIncomePaginatedByAnimal(int $animalId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->incomeRepository->getPaginatedByAnimal($animalId, $perPage);
    }

    /** @return LengthAwarePaginator<int, ExpenseTransaction> */
    public function getExpensePaginatedByAnimal(int $animalId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->expenseRepository->getPaginatedByAnimal($animalId, $perPage);
    }

    /** @return LengthAwarePaginator<int, IncomeTransaction> */
    public function getIncomePaginatedAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->incomeRepository->getPaginatedAll($perPage);
    }

    /** @return LengthAwarePaginator<int, ExpenseTransaction> */
    public function getExpensePaginatedAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->expenseRepository->getPaginatedAll($perPage);
    }

    /**
     * Get dashboard totals: per day (today), current month, and all time.
     *
     * @return array{today_income: float, today_expense: float, today_profit: float, month_income: float, month_expense: float, month_profit: float, total_income: float, total_expense: float, total_profit: float}
     */
    public function getDashboardTotals(): array
    {
        $today = Carbon::today();
        $currentMonth = Carbon::today()->startOfMonth();

        $todayIncome = $this->incomeRepository->getTotalForDate($today);
        $todayExpense = $this->expenseRepository->getTotalForDate($today)
            + $this->animalService->getTotalPurchasePriceForDate($today)
            - $this->expenseRepository->getTotalCowPurchaseForDate($today);
        $monthIncome = $this->incomeRepository->getTotalForMonth($currentMonth);
        $monthExpense = $this->expenseRepository->getTotalForMonth($currentMonth)
            + $this->animalService->getTotalPurchasePriceForMonth($currentMonth)
            - $this->expenseRepository->getTotalCowPurchaseForMonth($currentMonth);
        $totalIncome = $this->incomeRepository->getTotalAll();
        $totalExpense = $this->expenseRepository->getTotalAll()
            + $this->animalService->getTotalPurchasePriceAll()
            - $this->expenseRepository->getTotalCowPurchaseAll();

        return [
            'today_income' => $todayIncome,
            'today_expense' => $todayExpense,
            'today_profit' => $todayIncome - $todayExpense,
            'month_income' => $monthIncome,
            'month_expense' => $monthExpense,
            'month_profit' => $monthIncome - $monthExpense,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'total_profit' => $totalIncome - $totalExpense,
        ];
    }

    /**
     * Day-wise income and expense for a month (for dashboard table).
     * If $yearMonth is null or current month, only days 1..today are returned. Otherwise all days in that month.
     *
     * @param  string|null  $yearMonth  Format Y-m (e.g. 2026-02)
     * @return array<int, array{date: string, income: float, expense: float, profit: float}>
     */
    public function getDayWiseIncomeExpenseForMonth(?string $yearMonth = null): array
    {
        $today = Carbon::today();
        if ($yearMonth !== null && preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            $monthStart = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $isCurrentMonth = $monthStart->isSameMonth($today);
            $lastDay = $isCurrentMonth ? (int) $today->format('d') : (int) $monthStart->copy()->endOfMonth()->format('d');
        } else {
            $monthStart = $today->copy()->startOfMonth();
            $lastDay = (int) $today->format('d');
        }
        $incomeByDate = $this->incomeRepository->getDailySumsForMonth($monthStart);
        $expenseByDate = $this->expenseRepository->getDailySumsForMonth($monthStart);
        $cowPurchaseByDate = $this->expenseRepository->getDailyCowPurchaseSumsForMonth($monthStart);
        $animalPurchaseByDate = $this->animalService->getDailyPurchasePriceSumsForMonth($monthStart);

        $rows = [];
        for ($day = 1; $day <= $lastDay; $day++) {
            $date = $monthStart->copy()->day($day)->format('Y-m-d');
            $income = $incomeByDate[$date] ?? 0.0;
            $expenseRaw = $expenseByDate[$date] ?? 0.0;
            $expense = $expenseRaw
                + ($animalPurchaseByDate[$date] ?? 0.0)
                - ($cowPurchaseByDate[$date] ?? 0.0);
            $profit = $income - $expense;
            $rows[] = [
                'date' => $date,
                'income' => $income,
                'expense' => $expense,
                'profit' => $profit,
            ];
        }

        return array_reverse($rows);
    }

    /** @return array<int, array{date: string, income: float, expense: float, profit: float}> */
    public function getDayWiseIncomeExpenseForCurrentMonth(): array
    {
        return $this->getDayWiseIncomeExpenseForMonth(null);
    }
}
