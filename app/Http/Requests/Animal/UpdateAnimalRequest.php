<?php

namespace App\Http\Requests\Animal;

use App\Enums\Gender;
use App\Enums\Group;
use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAnimalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $animalId = $this->route('animal');

        return [
            'animal_id' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('animals', 'animal_id')->ignore($animalId)],
            'breed' => ['sometimes', 'required', 'string', 'max:255'],
            'gender' => ['sometimes', 'required', Rule::enum(Gender::class)],
            'date_of_birth' => ['nullable', 'date'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'current_weight' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', 'required', Rule::enum(Status::class)],
            'grouping' => ['nullable', Rule::enum(Group::class)],
        ];
    }
}
