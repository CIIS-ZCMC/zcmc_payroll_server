<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\EmployeeSalaryResource;
use App\Http\Resources\TimeRecordResource;

class EmployeeInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $excludedDetails = $this->getExclusionDetails()->where("month", $request->processMonth['month'])
            ->where('year', $request->processMonth['year'])
            ->first();
        if ($excludedDetails) {
            $reasons = json_decode($excludedDetails->reason);
        }

        return [
            'id' => $this->id,
            'employee_number' => $this->employee_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'designation' => $this->designation,
            'created' => $this->created_at,
            'assigned_area' => json_decode($this->assigned_area),
            'status' => $this->status,
            'is_newly_hired' => $this->is_newly_hired,
            'Salary' => EmployeeSalaryResource::collection([$this->getSalary]),
            'TimeRecord' => TimeRecordResource::collection([$this->getTimeRecords]),
            'Deduction' => EmployeeDeductionResource::collection($this->employeeDeductions),
            'Receivables' => EmployeeReceivableResource::collection($this->employeeReceivables),
            'isExcluded' => [
                'Details' => $excludedDetails,
                'Reason' => $reasons->reason ?? null,
                'Remarks' => $reasons->remarks ?? null,
                'Amount' => $reasons->Amount ?? null
            ]
        ];
    }
}
