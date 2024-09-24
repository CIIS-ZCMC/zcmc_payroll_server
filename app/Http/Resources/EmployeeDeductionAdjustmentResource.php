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
            'month' => $this->month,
            'year' => $this->year,
            'amount' => $this->amount,
            'amount_to_pay' => $this->amount_to_pay,
            'amount_balance' => $this->amount_balance,
            'reason' => $this->reason,
            'action_by' => $this->getActionBy(), // Decode JSON for action_by
            'created_at' => Carbon::parse($this->created_at)->format('M d, Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('M d, Y'),
        ];
    }

    // Helper function to decode action_by JSON
    private function getActionBy()
    {
        // Decode the JSON field action_by
        $actionBy = json_decode($this->action_by, true);

        // Return decoded data, handling missing fields gracefully
        return [
            'id' => $actionBy['employee_profile_id'],
            'employee_number' => $actionBy['employee_id'],
            'employee_name' => $actionBy['employee_name'],
            'area_assigned' => $actionBy['area_assigned'],
        ];
    }
}
