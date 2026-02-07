<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expense\StoreExpenseTransactionRequest;
use App\Http\Requests\Expense\UpdateExpenseTransactionRequest;
use App\Services\AnimalService;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseTransactionController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService,
        protected AnimalService $animalService
    ) {}

    public function index(Request $request, int $animal): Response
    {
        $animalModel = $this->animalService->getById($animal);
        $perPage = (int) $request->get('per_page', 15);
        $expense = $this->transactionService->getExpensePaginatedByAnimal($animal, $perPage);
        $expense->appends($request->only('per_page'));

        return Inertia::render('animals/expense/Index', [
            'animal' => $animalModel->load(['shed.farm']),
            'expense' => $expense,
        ]);
    }

    public function create(int $animal): Response
    {
        $animalModel = $this->animalService->getById($animal);

        return Inertia::render('animals/expense/Create', [
            'animal' => $animalModel->load(['shed.farm']),
        ]);
    }

    public function store(StoreExpenseTransactionRequest $request, int $animal): RedirectResponse
    {
        $validated = $request->validated();
        $validated['animal_id'] = $animal;
        $this->transactionService->createExpense($validated, $request->user()?->id);

        return redirect()->route('animals.expense.index', $animal)->with('success', 'Expense recorded successfully.');
    }

    public function edit(int $animal, int $expenseTransaction): Response
    {
        $animalModel = $this->animalService->getById($animal);
        $transaction = $this->transactionService->getExpenseById($expenseTransaction);
        if ($transaction->animal_id !== $animal) {
            abort(404);
        }

        return Inertia::render('animals/expense/Edit', [
            'animal' => $animalModel->load(['shed.farm']),
            'expenseTransaction' => $transaction,
        ]);
    }

    public function update(UpdateExpenseTransactionRequest $request, int $animal, int $expenseTransaction): RedirectResponse
    {
        $transaction = $this->transactionService->getExpenseById($expenseTransaction);
        if ($transaction->animal_id !== $animal) {
            abort(404);
        }
        $this->transactionService->updateExpense($expenseTransaction, $request->validated(), $request->user()?->id);

        return redirect()->route('animals.expense.index', $animal)->with('success', 'Expense updated successfully.');
    }

    public function destroy(int $animal, int $expenseTransaction): RedirectResponse
    {
        $transaction = $this->transactionService->getExpenseById($expenseTransaction);
        if ($transaction->animal_id !== $animal) {
            abort(404);
        }
        $this->transactionService->deleteExpense($expenseTransaction);

        return redirect()->route('animals.expense.index', $animal)->with('success', 'Expense deleted successfully.');
    }
}
