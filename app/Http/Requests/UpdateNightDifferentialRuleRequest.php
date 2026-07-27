<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNightDifferentialRuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employment_type' => ['required', 'string', 'in:permanent,contractual,temporary,job-order'],
            'start_time' => ['required', 'string', 'date_format:H:i'],
            'end_time' => ['required', 'string', 'date_format:H:i', 'after:start_time'],
            'rate_type' => ['required', 'string', 'in:percentage,fixed_per_minute,fixed_per_hour'],
            'rate' => ['required', 'numeric'],
            'effective_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
