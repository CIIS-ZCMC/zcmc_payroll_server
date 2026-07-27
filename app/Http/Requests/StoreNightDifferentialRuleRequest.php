<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNightDifferentialRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employment_type' => ['required', Rule::in(['permanent', 'contractual', 'temporary'])],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'rate_type' => ['required', Rule::in(['percentage', 'fixed_per_minute', 'fixed_per_hour'])],
            'rate' => ['required', 'numeric', 'min:0'],
            'effective_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:effective_date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
