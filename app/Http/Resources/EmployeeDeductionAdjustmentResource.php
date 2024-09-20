<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDeductionAdjustmentResource extends JsonResource
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
                'id' => $this->deduction->id,
                'employee_list_id' => $this->employeeDeduction->employee_list_id,
                'deduction_id' => $this->employeeDeduction->deduction_id,
                'expected_amount' => $this->employeeDeduction->amount
            ],
            'employee' => [
                'id' => $this->employeeList->id,
                'employee_number' => $this->employeeList->employee_number,
                'name' => $this->employeeList->last_name . "," . $this->employeeList->first_name . " " . $this->employeeList->middle_name,
                'designation' => $this->employeeList->designation,
            ],
            'deduction' => [
                'id' => $this->deduction->id,
                'name' => $this->deduction->name
            ],
            'action_by' => [
                'id' => $this->employeeList->id,
                'employee_number' => $this->employeeList->employee_number,
                'name' => $this->employeeList->last_name . "," . $this->employeeList->first_name . " " . $this->employeeList->middle_name,
                'designation' => $this->employeeList->designation,
            ],
            'month' => $this->month,
            'year' => $this->year,
            'amount' => $this->amount,
            'reason' => $this->reason,
            'created_at' => Carbon::parse($this->created_at)->format('M d, Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('M d, Y'),
        ];
    }
}
