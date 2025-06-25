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

        return [
            'id' => $this->id,
            'employee_profile_id' => $this->employee_profile_id,
            'employee_number' => $this->employee_number,
            'full_name' => $this->last_name . ', ' . $this->first_name . ' ' . ($this->middle_name ? strtoupper(substr($this->middle_name, 0, 1)) . '.' : ''),
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
            'salary' => new EmployeeSalaryResource($this->employeeSalary),
            'deductions' => EmployeeDeductionResource::collection($this->whenLoaded('employeeDeductions')),
            'receivables' => EmployeeReceivableResource::collection($this->whenLoaded('employeeReceivables')),
            'deduction_group' => $this->groupDeductionsByGroup()
        ];
    }

    public function groupDeductionsByGroup()
    {
        $grouped = [];

        $deductions = $this->employeeDeductions->load('deductions.deductionGroup');

        foreach ($deductions as $deduction) {
            $group = $deduction->deductions->deductionGroup ?? null;

            if (!$group) {
                $groupName = 'Ungrouped';
                $groupId = null;
            } else {
                $groupName = $group->name;
                $groupId = $group->id;
            }

            if (!isset($grouped[$groupName])) {
                $grouped[$groupName] = [
                    'group_id' => $groupId,
                    'group_name' => $groupName,
                    'deductions' => []
                ];
            }

            $grouped[$groupName]['deductions'][] = new EmployeeDeductionResource($deduction);
        }

        return array_values($grouped);
    }
}
