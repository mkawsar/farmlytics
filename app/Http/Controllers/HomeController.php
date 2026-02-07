<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    public function index(): Response
    {
        $totals = $this->transactionService->getDashboardTotals();

        return Inertia::render('home/Index', [
            'totals' => $totals,
        ]);
    }
}
