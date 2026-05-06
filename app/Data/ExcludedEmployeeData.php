<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class ExcludedEmployeeData extends Data
{
    public function __construct(
        public int $employee_id,
        public int $payroll_period_id,
        public string $reason,
        public bool $is_removed,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->employee_id,
            $request->payroll_period_id,
            $request->reason,
            $request->is_removed,
        );
    }

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employee_id,
            'payroll_period_id' => $this->payroll_period_id,
            'reason' => $this->reason,
            'is_removed' => $this->is_removed,
        ];
    }
}