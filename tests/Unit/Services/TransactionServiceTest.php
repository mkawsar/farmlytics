<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\IncomeType;
use App\Models\Animal;
use App\Models\ExpenseTransaction;
use App\Models\IncomeTransaction;
use App\Repositories\ExpenseTransactionRepository;
use App\Repositories\IncomeTransactionRepository;
use App\Services\AnimalService;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\ServiceTestCase;

class TransactionServiceTest extends ServiceTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function create_income_for_animal_delegates_to_repo_with_animal_shed_and_farm_ids(): void
    {
        /*
         * Given: an animal with shed_id and farm_id, and income data (milk_sale)
         * When: we create an income transaction for that animal
         * Then: the repository is called with animal_id, shed_id and farm_id from the animal, and the created transaction is returned (animal status is not updated)
         */
        $animalId = 1;
        $animal = new Animal;
        $animal->id = $animalId;
        $animal->shed_id = 10;
        $animal->farm_id = 20;

        $data = [
            'income_type' => IncomeType::MILK_SALE->value,
            'amount' => 1000,
            'transaction_date' => '2026-02-07',
        ];
        $transaction = new IncomeTransaction;
        $transaction->id = 1;

        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $incomeRepo->shouldReceive('createTransaction')
            ->once()
            ->with(Mockery::on(function (array $arg) use ($animalId) {
                return $arg['animal_id'] === $animalId
                    && $arg['shed_id'] === 10
                    && $arg['farm_id'] === 20
                    && isset($arg['income_type'], $arg['amount'], $arg['transaction_date']);
            }))
            ->andReturn($transaction);

        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $animalService = Mockery::mock(AnimalService::class);
        $animalService->shouldReceive('getById')->once()->with($animalId)->andReturn($animal);
        $animalService->shouldReceive('update')->never();

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $result = $service->createIncomeForAnimal($animalId, $data);

        $this->assertInstanceOf(IncomeTransaction::class, $result);
        $this->assertSame($transaction, $result);
    }

    #[Test]
    public function create_income_for_animal_with_animal_sale_updates_animal_status_to_sold(): void
    {
        /*
         * Given: an animal and income data with type animal_sale
         * When: we create an income transaction for that animal with user id
         * Then: the income is created and the animal is updated to status sold
         */
        $animalId = 1;
        $animal = new Animal;
        $animal->id = $animalId;
        $animal->shed_id = 10;
        $animal->farm_id = 20;

        $data = [
            'income_type' => IncomeType::ANIMAL_SALE->value,
            'amount' => 50000,
            'transaction_date' => '2026-02-07',
        ];
        $transaction = new IncomeTransaction;
        $transaction->id = 1;

        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $incomeRepo->shouldReceive('createTransaction')->once()->andReturn($transaction);

        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $animalService = Mockery::mock(AnimalService::class);
        $animalService->shouldReceive('getById')->once()->with($animalId)->andReturn($animal);
        $animalService->shouldReceive('update')
            ->once()
            ->with($animalId, ['status' => 'sold'], 42);

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $service->createIncomeForAnimal($animalId, $data, 42);
    }

    #[Test]
    public function create_expense_with_animal_id_sets_shed_id_and_farm_id_from_animal(): void
    {
        /*
         * Given: an animal with shed_id and farm_id, and expense data with animal_id
         * When: we create an expense with that data
         * Then: the repository is called with shed_id and farm_id from the animal, and the created transaction is returned
         */
        $animal = new Animal;
        $animal->id = 5;
        $animal->shed_id = 11;
        $animal->farm_id = 22;

        $data = [
            'animal_id' => 5,
            'expense_type' => 'fodder',
            'amount' => 500,
            'transaction_date' => '2026-02-07',
        ];
        $transaction = new ExpenseTransaction;
        $transaction->id = 1;

        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $expenseRepo->shouldReceive('createTransaction')
            ->once()
            ->with(Mockery::on(function (array $arg) {
                return $arg['animal_id'] === 5 && $arg['shed_id'] === 11 && $arg['farm_id'] === 22;
            }))
            ->andReturn($transaction);

        $animalService = Mockery::mock(AnimalService::class);
        $animalService->shouldReceive('getById')->once()->with(5)->andReturn($animal);

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $result = $service->createExpense($data);

        $this->assertInstanceOf(ExpenseTransaction::class, $result);
        $this->assertSame($transaction, $result);
    }

    #[Test]
    public function get_dashboard_totals_returns_expected_keys_and_profit_calculation(): void
    {
        /*
         * Given: repositories that return known totals (income and expense for today, month, and all time)
         * When: we request dashboard totals
         * Then: the result has all expected keys and profit equals income minus expense
         */
        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $incomeRepo->shouldReceive('getTotalForDate')->once()->andReturn(100.0);
        $incomeRepo->shouldReceive('getTotalForMonth')->once()->andReturn(2000.0);
        $incomeRepo->shouldReceive('getTotalAll')->once()->andReturn(50000.0);

        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $expenseRepo->shouldReceive('getTotalForDate')->once()->andReturn(50.0);
        $expenseRepo->shouldReceive('getTotalCowPurchaseForDate')->once()->andReturn(0.0);
        $expenseRepo->shouldReceive('getTotalForMonth')->once()->andReturn(1500.0);
        $expenseRepo->shouldReceive('getTotalCowPurchaseForMonth')->once()->andReturn(0.0);
        $expenseRepo->shouldReceive('getTotalAll')->once()->andReturn(30000.0);
        $expenseRepo->shouldReceive('getTotalCowPurchaseAll')->once()->andReturn(0.0);

        $animalService = Mockery::mock(AnimalService::class);
        $animalService->shouldReceive('getTotalPurchasePriceForDate')->once()->andReturn(0.0);
        $animalService->shouldReceive('getTotalPurchasePriceForMonth')->once()->andReturn(0.0);
        $animalService->shouldReceive('getTotalPurchasePriceAll')->once()->andReturn(0.0);

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $result = $service->getDashboardTotals();

        $this->assertArrayHasKey('today_income', $result);
        $this->assertArrayHasKey('today_expense', $result);
        $this->assertArrayHasKey('today_profit', $result);
        $this->assertArrayHasKey('month_income', $result);
        $this->assertArrayHasKey('month_expense', $result);
        $this->assertArrayHasKey('month_profit', $result);
        $this->assertArrayHasKey('total_income', $result);
        $this->assertArrayHasKey('total_expense', $result);
        $this->assertArrayHasKey('total_profit', $result);

        $this->assertSame(100.0, $result['today_income']);
        $this->assertSame(50.0, $result['today_expense']);
        $this->assertSame(50.0, $result['today_profit']);
        $this->assertSame(2000.0 - 1500.0, $result['month_profit']);
        $this->assertSame(50000.0 - 30000.0, $result['total_profit']);
    }

    #[Test]
    public function get_day_wise_income_expense_for_month_returns_rows_with_date_income_expense_profit(): void
    {
        /*
         * Given: test date set to Feb 2026, and repositories returning daily sums for January 2026
         * When: we request day-wise income/expense for January 2026
         * Then: we get one row per day with date, income, expense, profit (31 days for January)
         */
        $this->mockTestDate('2026-02-15 12:00:00');
        $incomeByDate = ['2026-01-01' => 100.0, '2026-01-02' => 200.0];
        $expenseByDate = ['2026-01-01' => 50.0];

        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $incomeRepo->shouldReceive('getDailySumsForMonth')
            ->once()
            ->with(Mockery::on(function (Carbon $d) {
                return $d->format('Y-m') === '2026-01';
            }))
            ->andReturn($incomeByDate);

        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $expenseRepo->shouldReceive('getDailySumsForMonth')->once()->andReturn($expenseByDate);
        /** @var array<string, float> $emptyDaily */
        $emptyDaily = [];
        $expenseRepo->shouldReceive('getDailyCowPurchaseSumsForMonth')->once()->andReturn($emptyDaily);

        $animalService = Mockery::mock(AnimalService::class);
        $animalService->shouldReceive('getDailyPurchasePriceSumsForMonth')->once()->andReturn($emptyDaily);

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $result = $service->getDayWiseIncomeExpenseForMonth('2026-01');

        $this->assertIsArray($result);
        $this->assertCount(31, $result);
        $first = $result[0];
        $this->assertArrayHasKey('date', $first);
        $this->assertArrayHasKey('income', $first);
        $this->assertArrayHasKey('expense', $first);
        $this->assertArrayHasKey('profit', $first);
        $this->assertSame('2026-01-31', $result[0]['date']);
        $this->assertSame('2026-01-01', $result[30]['date']);
        $this->resetTestDate();
    }

    #[Test]
    public function get_day_wise_income_expense_for_current_month_delegates_to_get_day_wise_for_month_null(): void
    {
        /*
         * Given: test date set to 7 Feb 2026, and repositories returning empty daily sums
         * When: we request day-wise for current month (null)
         * Then: we get rows only for days 1..7 (today), most recent first
         */
        $this->mockTestDate('2026-02-07 12:00:00');
        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $incomeRepo->shouldReceive('getDailySumsForMonth')->once()->andReturn([]);
        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $expenseRepo->shouldReceive('getDailySumsForMonth')->once()->andReturn([]);
        $expenseRepo->shouldReceive('getDailyCowPurchaseSumsForMonth')->once()->andReturn([]);
        $animalService = Mockery::mock(AnimalService::class);
        $animalService->shouldReceive('getDailyPurchasePriceSumsForMonth')->once()->andReturn([]);

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $result = $service->getDayWiseIncomeExpenseForCurrentMonth();

        $this->assertCount(7, $result);
        $this->assertSame('2026-02-07', $result[0]['date']);
        $this->resetTestDate();
    }

    #[Test]
    public function update_income_delegates_to_repository_and_adds_updated_by_when_user_provided(): void
    {
        /*
         * Given: an income transaction id, update data, and a user id
         * When: we update the income with user id 99
         * Then: the repository is called with updated_by set, and the transaction is returned
         */
        $id = 1;
        $data = ['amount' => 1500];
        $transaction = new IncomeTransaction;
        $transaction->id = $id;

        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $incomeRepo->shouldReceive('updateTransaction')
            ->once()
            ->with($id, Mockery::on(function (array $arg) {
                return $arg['updated_by'] === 99;
            }))
            ->andReturn($transaction);
        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $animalService = Mockery::mock(AnimalService::class);

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $result = $service->updateIncome($id, $data, 99);

        $this->assertSame($transaction, $result);
    }

    #[Test]
    public function update_expense_delegates_to_repository(): void
    {
        /*
         * Given: an expense transaction id and update data
         * When: we update the expense
         * Then: the repository is called and the transaction is returned
         */
        $id = 1;
        $data = ['amount' => 800];
        $transaction = new ExpenseTransaction;
        $transaction->id = $id;

        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $expenseRepo->shouldReceive('updateTransaction')->once()->with($id, $data)->andReturn($transaction);
        $animalService = Mockery::mock(AnimalService::class);

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $result = $service->updateExpense($id, $data);

        $this->assertSame($transaction, $result);
    }

    #[Test]
    public function get_income_by_id_delegates_to_repository(): void
    {
        /*
         * Given: an income transaction id and a repository that returns that transaction
         * When: we get income by id
         * Then: the repository is called and the transaction is returned
         */
        $id = 1;
        $transaction = new IncomeTransaction;
        $transaction->id = $id;

        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $incomeRepo->shouldReceive('getTransactionById')->once()->with($id)->andReturn($transaction);
        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $animalService = Mockery::mock(AnimalService::class);

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $result = $service->getIncomeById($id);

        $this->assertSame($transaction, $result);
    }

    #[Test]
    public function get_expense_by_id_delegates_to_repository(): void
    {
        /*
         * Given: an expense transaction id and a repository that returns that transaction
         * When: we get expense by id
         * Then: the repository is called and the transaction is returned
         */
        $id = 1;
        $transaction = new ExpenseTransaction;
        $transaction->id = $id;

        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $expenseRepo->shouldReceive('getTransactionById')->once()->with($id)->andReturn($transaction);
        $animalService = Mockery::mock(AnimalService::class);

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $result = $service->getExpenseById($id);

        $this->assertSame($transaction, $result);
    }

    #[Test]
    public function delete_income_delegates_to_repository(): void
    {
        /*
         * Given: an income transaction id and a repository that returns true on delete
         * When: we delete the income
         * Then: the repository delete is called and true is returned
         */
        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $incomeRepo->shouldReceive('deleteTransaction')->once()->with(1)->andReturn(true);
        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $animalService = Mockery::mock(AnimalService::class);

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $result = $service->deleteIncome(1);

        $this->assertTrue($result);
    }

    #[Test]
    public function delete_expense_delegates_to_repository(): void
    {
        /*
         * Given: an expense transaction id and a repository that returns true on delete
         * When: we delete the expense
         * Then: the repository delete is called and true is returned
         */
        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $expenseRepo->shouldReceive('deleteTransaction')->once()->with(1)->andReturn(true);
        $animalService = Mockery::mock(AnimalService::class);

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $result = $service->deleteExpense(1);

        $this->assertTrue($result);
    }

    #[Test]
    public function get_income_paginated_by_animal_delegates_to_repository(): void
    {
        /*
         * Given: an animal id and a repository that returns a paginator
         * When: we get income paginated by animal
         * Then: the repository is called and the paginator is returned
         */
        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $incomeRepo->shouldReceive('getPaginatedByAnimal')->once()->with(1, 15)->andReturn($paginator);
        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $animalService = Mockery::mock(AnimalService::class);

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $result = $service->getIncomePaginatedByAnimal(1, 15);

        $this->assertSame($paginator, $result);
    }

    #[Test]
    public function get_expense_paginated_by_animal_delegates_to_repository(): void
    {
        /*
         * Given: an animal id and a repository that returns a paginator
         * When: we get expense paginated by animal
         * Then: the repository is called and the paginator is returned
         */
        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $incomeRepo = Mockery::mock(IncomeTransactionRepository::class);
        $expenseRepo = Mockery::mock(ExpenseTransactionRepository::class);
        $expenseRepo->shouldReceive('getPaginatedByAnimal')->once()->with(1, 15)->andReturn($paginator);
        $animalService = Mockery::mock(AnimalService::class);

        $service = new TransactionService($incomeRepo, $expenseRepo, $animalService);
        $result = $service->getExpensePaginatedByAnimal(1, 15);

        $this->assertSame($paginator, $result);
    }
}
