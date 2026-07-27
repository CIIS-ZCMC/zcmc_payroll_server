<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class EmployeeComputedSalaryData extends Data
{
    
    public function __construct(
        public int $employee_id,
        public int $employee_time_record_id,
        public float $computed_salary,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['employee_id'],
            $request['employee_time_record_id'],
            $request['computed_salary']
        );
    }

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employee_id,
            'employee_time_record_id' => $this->employee_time_record_id,
            'computed_salary' => $this->computed_salary,
        ];
    }
}