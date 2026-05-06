<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayrollProcessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'payroll_period_id' => 'required|integer',
            'payroll_type' => 'required|integer',
            'current_step' => 'required|integer',
            'status' => 'required|string',
            'started_by' => 'required|string',
        ];
    }
}
