<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDeductionTrailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'employee_deduction' => [
                'id' => $this->employeeDeduction->id,
                'employee_list_id' => $this->employeeDeduction->employee_list_id,
                'deduction_id' => $this->employeeDeduction->deduction_id,
                'expected_amount' => $this->employeeDeduction->amount,
                'status' => $this->employeeDeduction->status
            ],
            'total_term' => $this->total_term,
            'total_term_paid' => $this->total_term_paid,
            'amount_paid' => $this->amount_paid,
            'date_paid' => $this->date_paid,
            'balance' => $this->balance,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'is_last_payment' => $this->is_last_payment,
            'is_adjustment' => $this->is_adjustment,
        ];
    }
}
