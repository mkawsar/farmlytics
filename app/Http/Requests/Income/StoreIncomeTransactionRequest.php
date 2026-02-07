<?php

namespace App\Http\Requests\Income;

use App\Enums\IncomeType;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncomeTransactionRequest extends FormRequest
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
            'income_type' => ['required', Rule::enum(IncomeType::class)],
            'amount' => ['required', 'numeric', 'min:0'],
            'quantity_liter' => ['nullable', 'numeric', 'min:0'],
            'rate_per_liter' => ['nullable', 'numeric', 'min:0'],
            'transaction_date' => ['required', 'date'],
            'buyer' => ['nullable', 'string', 'max:255'],
            'payment_status' => ['nullable', Rule::enum(PaymentStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
