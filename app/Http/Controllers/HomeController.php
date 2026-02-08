<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    public function index(Request $request): Response
    {
        $totals = $this->transactionService->getDashboardTotals();
        $month = $request->query('month');
        $dayWise = $this->transactionService->getDayWiseIncomeExpenseForMonth($month);
        $selectedMonth = $month ?? now()->format('Y-m');

        return Inertia::render('home/Index', [
            'totals' => $totals,
            'dayWise' => $dayWise,
            'selectedMonth' => $selectedMonth,
        ]);
    }
}
