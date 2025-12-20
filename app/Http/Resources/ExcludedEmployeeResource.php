<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ExcludedEmployeeResource extends JsonResource
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
            'employee' => new EmployeeResource($this->whenloaded('employee')),
            'payroll_period' => new PayrollPeriodResource($this->whenloaded('payrollPeriod')),
            'reason' => $this->reason,
            'is_removed' => $this->is_removed,
        ];
    }

    protected function formatExcludedPeriod()
    {
        $monthName = Carbon::createFromFormat('!m', $this->month)->format('F'); // Full month name
        return "{$monthName} {$this->year} ({$this->period_start}-{$this->period_end})";
    }
}
