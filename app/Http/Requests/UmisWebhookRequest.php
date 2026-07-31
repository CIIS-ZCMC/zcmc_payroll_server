<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UmisWebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization happens in VerifyUmisWebhookSignature, before the request
     * ever reaches this class.
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
     * Bounds mirror UMIS's own EmployeeTimeRecordController so a period this
     * side accepts is always one that side could have built.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'event' => 'required|string',
            'event_id' => 'required|string',
            'period' => 'required|array',
            'period.year' => 'required|integer|min:2020|max:2030',
            'period.month' => 'required|integer|min:1|max:12',
            'period.employment_type' => 'required|string|in:regular,job_order',
            'period.period_type' => 'required|string|in:first_half,second_half',
        ];
    }
}
