<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDeductionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $basic_salary = optional($this->employeeList->salary)->basic_salary ?? 0;
        $total_deductions = $this->calculateTotalDeductions();
        $net_salary = $this->calculateNetSalary($total_deductions);
        return [
            'employee_list' => new EmployeeListResource($this->whenLoaded('employeeList')),
            'deductions' => new DeductionResource($this->whenLoaded('deductions')),
            'employee_list_id' => $this->employee_list_id,
            'deduction_id' => $this->deduction_id,
            'amount' => $this->amount,
            'percentage' => $this->percentage,
            'frequency' => $this->frequency,
            'total_term' => $this->total_term,
            'is_default' => $this->is_default,
           'basic_salary' => $basic_salary,
            'total_deductions' => $total_deductions,
            'net_salary' => $net_salary,
        ];
    }

    private function calculateTotalDeductions()
    {
        return $this->is_default
            ? ($this->deductions ? $this->deductions->amount : 0)
            : $this->amount;
    }

    private function calculateNetSalary($total_deductions)
    {
        $basic_salary = $this->employeeList && $this->employeeList->salary ? $this->employeeList->salary->basic_salary : 0;
        return $basic_salary - $total_deductions;
    }
}
