<?php

namespace App\Http\Controllers;

use App\Services\AnimalService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService,
        protected AnimalService $animalService
    ) {}

    public function index(Request $request): Response
    {
        $totals = $this->transactionService->getDashboardTotals();
        $month = $request->query('month');
        $dayWise = $this->transactionService->getDayWiseIncomeExpenseForMonth($month);
        $selectedMonth = $month ?? now()->format('Y-m');
        $activeAnimals = $this->animalService->getActiveAnimals();

        return Inertia::render('home/Index', [
            'totals' => $totals,
            'dayWise' => $dayWise,
            'selectedMonth' => $selectedMonth,
            'activeAnimals' => $activeAnimals,
        ]);
    }
}
