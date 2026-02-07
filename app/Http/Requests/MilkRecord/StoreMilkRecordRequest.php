<?php

namespace App\Http\Requests\MilkRecord;

use Illuminate\Foundation\Http\FormRequest;

class StoreMilkRecordRequest extends FormRequest
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
            'record_date' => ['required', 'date'],
            'quantity_liter' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
