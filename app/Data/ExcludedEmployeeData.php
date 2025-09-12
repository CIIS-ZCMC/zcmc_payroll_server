<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class ExcludedEmployeeData extends Data
{
    public function __construct(
        public int $employee_id,
        public int $payroll_period_id,
        public string $month,
        public string $year,
        public string $period_start,
        public string $period_end,
        public string $reason,
        public bool $is_removed,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->employee_id,
            $request->payroll_period_id,
            $request->month,
            $request->year,
            $request->period_start,
            $request->period_end,
            $request->reason,
            $request->is_removed,
        );
    }

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employee_id,
            'payroll_period_id' => $this->payroll_period_id,
            'month' => $this->month,
            'year' => $this->year,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'reason' => $this->reason,
            'is_removed' => $this->is_removed,
        ];
    }
}