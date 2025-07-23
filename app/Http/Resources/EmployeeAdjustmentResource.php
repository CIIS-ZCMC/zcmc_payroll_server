<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeAdjustmentResource extends JsonResource
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
            'payroll_period' => new PayrollPeriodResource($this->payrollPeriod),
            'employee_deduction' => new EmployeeDeductionResource($this->employeeDeduction),
            'employee_receivable' => new EmployeeReceivableResource($this->employeeReceivable),
            'amount' => number_format($this->amount, 2),
            'amount_to_pay' => number_format($this->amount_to_pay, 2),
            'amount_balance' => number_format($this->amount_balance, 2),
            'reason' => $this->reason,
        ];
    }
}
