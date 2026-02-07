<?php

namespace App\Http\Controllers;

use App\Http\Requests\Income\StoreIncomeTransactionRequest;
use App\Http\Requests\Income\UpdateIncomeTransactionRequest;
use App\Services\AnimalService;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IncomeTransactionController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService,
        protected AnimalService $animalService
    ) {}

    public function index(Request $request, int $animal): Response
    {
        $animalModel = $this->animalService->getById($animal);
        $perPage = (int) $request->get('per_page', 15);
        $income = $this->transactionService->getIncomePaginatedByAnimal($animal, $perPage);
        $income->appends($request->only('per_page'));

        return Inertia::render('animals/income/Index', [
            'animal' => $animalModel->load(['shed.farm']),
            'income' => $income,
        ]);
    }

    public function create(int $animal): Response
    {
        $animalModel = $this->animalService->getById($animal);

        return Inertia::render('animals/income/Create', [
            'animal' => $animalModel->load(['shed.farm']),
        ]);
    }

    public function store(StoreIncomeTransactionRequest $request, int $animal): RedirectResponse
    {
        $this->transactionService->createIncomeForAnimal(
            $animal,
            $request->validated(),
            $request->user()?->id
        );

        return redirect()->route('animals.income.index', $animal)->with('success', 'Income recorded successfully.');
    }

    public function edit(int $animal, int $incomeTransaction): Response
    {
        $animalModel = $this->animalService->getById($animal);
        $transaction = $this->transactionService->getIncomeById($incomeTransaction);
        if ($transaction->animal_id !== $animal) {
            abort(404);
        }

        return Inertia::render('animals/income/Edit', [
            'animal' => $animalModel->load(['shed.farm']),
            'incomeTransaction' => $transaction,
        ]);
    }

    public function update(UpdateIncomeTransactionRequest $request, int $animal, int $incomeTransaction): RedirectResponse
    {
        $transaction = $this->transactionService->getIncomeById($incomeTransaction);
        if ($transaction->animal_id !== $animal) {
            abort(404);
        }
        $this->transactionService->updateIncome($incomeTransaction, $request->validated(), $request->user()?->id);

        return redirect()->route('animals.income.index', $animal)->with('success', 'Income updated successfully.');
    }

    public function destroy(int $animal, int $incomeTransaction): RedirectResponse
    {
        $transaction = $this->transactionService->getIncomeById($incomeTransaction);
        if ($transaction->animal_id !== $animal) {
            abort(404);
        }
        $this->transactionService->deleteIncome($incomeTransaction);

        return redirect()->route('animals.income.index', $animal)->with('success', 'Income deleted successfully.');
    }
}
