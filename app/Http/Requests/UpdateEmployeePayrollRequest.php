<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'basic_pay' => ['required', 'numeric', 'min:0'],
            'gross_pay' => ['required', 'numeric', 'min:0'],
            'total_deductions' => ['required', 'numeric', 'min:0'],
            'total_receivables' => ['nullable', 'numeric', 'min:0'],
            'total_adjustments' => ['nullable', 'numeric'],
            'net_pay' => ['required', 'numeric', 'min:0'],
            'first_half' => ['nullable', 'numeric', 'min:0'],
            'second_half' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
