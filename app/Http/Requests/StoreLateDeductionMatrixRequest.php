<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLateDeductionMatrixRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employment_type' => 'required|string|max:255',
            'from_minutes' => 'required|integer|min:0',
            'to_minutes' => 'required|integer|min:0',
            'amount' => 'required|numeric|min:0',
        ];
    }
}
