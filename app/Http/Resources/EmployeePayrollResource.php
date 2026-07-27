<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeePayrollResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'payroll_period_id' => $this->payroll_period_id,
            'period' => new PayrollPeriodResource($this->whenLoaded('period')),
            'payroll_run_id' => $this->payroll_run_id,
            'run' => new PayrollRunResource($this->whenLoaded('run')),
            'basic_pay' => $this->basic_pay,
            'gross_pay' => $this->gross_pay,
            'total_deductions' => $this->total_deductions,
            'total_receivables' => $this->total_receivables,
            'total_adjustments' => $this->total_adjustments,
            'net_pay' => $this->net_pay,
            'first_half' => $this->first_half,
            'second_half' => $this->second_half,
            'locked_at' => $this->locked_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
