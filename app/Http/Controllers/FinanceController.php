<?php

namespace App\Http\Controllers;

use App\Services\AnimalService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FinanceController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService,
        protected AnimalService $animalService
    ) {}

    /**
     * Global income list with "Add income" entry point.
     */
    public function indexIncome(Request $request): Response
    {
        $perPage = (int) $request->get('per_page', 15);
        $income = $this->transactionService->getIncomePaginatedAll($perPage);
        $income->appends($request->only('per_page'));

        return Inertia::render('finance/IncomeList', [
            'income' => $income,
        ]);
    }

    /**
     * Select an animal to add income for.
     */
    public function selectAnimalForIncome(Request $request): Response
    {
        $perPage = (int) $request->get('per_page', 15);
        $search = $request->get('search', '');
        $animals = $this->animalService->getPaginated($search ?: null, $perPage);
        $animals->appends($request->only('search', 'per_page'));

        return Inertia::render('finance/SelectAnimal', [
            'animals' => $animals,
            'type' => 'income',
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Global expense list with "Add expense" entry point.
     */
    public function indexExpense(Request $request): Response
    {
        $perPage = (int) $request->get('per_page', 15);
        $expense = $this->transactionService->getExpensePaginatedAll($perPage);
        $expense->appends($request->only('per_page'));

        return Inertia::render('finance/ExpenseList', [
            'expense' => $expense,
        ]);
    }

    /**
     * Select an animal to add expense for.
     */
    public function selectAnimalForExpense(Request $request): Response
    {
        $perPage = (int) $request->get('per_page', 15);
        $search = $request->get('search', '');
        $animals = $this->animalService->getPaginated($search ?: null, $perPage);
        $animals->appends($request->only('search', 'per_page'));

        return Inertia::render('finance/SelectAnimal', [
            'animals' => $animals,
            'type' => 'expense',
            'filters' => ['search' => $search],
        ]);
    }
}
