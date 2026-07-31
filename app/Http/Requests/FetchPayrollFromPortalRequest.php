<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchPayrollFromPortalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'employment_type' => ['required', 'string', 'in:regular,job-order'],
            'period_type' => ['required', 'string', 'in:first_half,second_half'],
        ];
    }
}
