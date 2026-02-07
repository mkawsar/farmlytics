<?php

use App\Enums\ExpenseType;
use App\Models\Animal;
use App\Models\ExpenseTransaction;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('farmlytics:backfill-purchase-expenses', function () {
    $transactionService = app(TransactionService::class);
    $animals = Animal::query()
        ->whereNotNull('purchase_price')
        ->where('purchase_price', '>', 0)
        ->get();
    $created = 0;
    foreach ($animals as $animal) {
        $hasExpense = ExpenseTransaction::query()
            ->where('animal_id', $animal->id)
            ->where('expense_type', ExpenseType::COW_PURCHASE->value)
            ->exists();
        if ($hasExpense) {
            continue;
        }
        $transactionService->createExpense([
            'animal_id' => $animal->id,
            'expense_type' => ExpenseType::COW_PURCHASE->value,
            'amount' => (float) $animal->purchase_price,
            'transaction_date' => $animal->purchase_date
                ? Carbon::parse($animal->purchase_date)->toDateString()
                : $animal->created_at->toDateString(),
        ], null);
        $created++;
    }
    $this->info("Created {$created} cow purchase expense(s) for animals that had none.");
})->purpose('Create COW_PURCHASE expense records for existing animals with purchase_price');
