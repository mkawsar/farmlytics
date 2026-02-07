<?php

namespace App\Http\Requests\Expense;

use App\Enums\ExpenseType;
use App\Enums\FeedType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'expense_type' => ['sometimes', 'required', Rule::enum(ExpenseType::class)],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'feed_type' => ['nullable', Rule::enum(FeedType::class)],
            'transaction_date' => ['sometimes', 'required', 'date'],
            'period_month' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
