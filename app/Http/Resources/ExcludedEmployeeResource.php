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
            'month' => $this->month,
            'year' => $this->year,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'reason' => $this->reason,
            'excluded_at' => $this->formatExcludedPeriod(),
            'is_removed' => $this->is_removed,
        ];
    }

    protected function formatExcludedPeriod()
    {
        $monthName = Carbon::createFromFormat('!m', $this->month)->format('F'); // Full month name
        return "{$monthName} {$this->year} ({$this->period_start}-{$this->period_end})";
    }
}
