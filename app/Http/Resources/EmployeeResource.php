<?php

namespace App\Http\Resources;

use App\Models\EmployeeComputedSalary;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $area = json_decode($this->assigned_area, true);

        $groupedDeductions = $this->employeeDeductions
            ->groupBy(function ($item) {
                return $item->deductions?->deduction_group_id;
            })
            ->map(function ($items) {

                $group = $items->first()->deductions?->deductionGroup;

                return [
                    'group_id' => $group?->id,
                    'group_name' => $group?->name,
                    'group_total' => $items->sum('amount'),
                    'employee_id' => $this->id,
                    'deduction_details' => $items->map(function ($deduction) {
                        return [
                            'id' => $deduction->id,
                            'employee_id' => $deduction->employee_id,
                            'payroll_period_id' => $deduction->payroll_period_id,
                            'deduction_id' => $deduction->deduction_id,
                            'deduction_group_id' => $deduction->deductions?->deduction_group_id,
                            'name' => $deduction->deductions?->name,
                            'code' => $deduction->deductions?->code,
                            'amount' => $deduction->amount,
                            'billing_cycle' => $deduction->billing_cycle,
                        ];
                    })->values(),
                ];
            })
            ->values();

        return [
            'id' => $this->id,
            'employee_profile_id' => $this->employee_profile_id,
            'employee_number' => $this->employee_number,

            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'ext_name' => $this->ext_name,

            'designation' => $this->designation,
            'assigned_area' => [
                'details' => [
                    'id' => $area['details']['id'] ?? null,
                    'name' => $area['details']['name'] ?? null,
                    'code' => $area['details']['code'] ?? null,
                ],
                'sector' => $area['sector'] ?? null
            ],

            'status' => $this->status,
            'is_newly_hired' => $this->is_newly_hired,
            'is_excluded' => $this->is_excluded,
            'is_resigned' => $this->is_resigned,

            'salary' => new EmployeeSalaryResource($this->whenLoaded('employeeSalary')),

            'computed_salary' => new EmployeeComputedSalaryResource($this->whenLoaded('employeeComputedSalary')),

            'deductions' => EmployeeDeductionResource::collection($this->whenLoaded('employeeDeductions')),

            'grouped_deductions' => $groupedDeductions ?? [],

            'receivables' => EmployeeReceivableResource::collection($this->whenLoaded('employeeReceivables')),

            'employee_time_records' => new EmployeeTimeRecordResource($this->whenLoaded('employeeTimeRecords')),
            // 'deduction_group' => $this->groupDeductionsByGroup(),

            'excluded' => $this->whenLoaded('excludedEmployees', function () {
                return new ExcludedEmployeeResource($this->excludedEmployees->first());
            })
        ];
    }
}
