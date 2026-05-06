<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class EmployeePayrollData extends Data
{
    public function __construct(
        public int $employee_id,
        public int $employee_time_record_id,
        public int $payroll_period_id,
        public string $month,
        public string $year,
        public float $gross_salary,
        public float $total_deductions,
        public float $total_receivables,
        public float $net_pay,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['employee_id'],
            $request['employee_time_record_id'],
            $request['payroll_period_id'],
            $request['month'],
            $request['year'],
            $request['gross_salary'],
            $request['total_deductions'],
            $request['total_receivables'],
            $request['net_pay'],
        );
    }

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employee_id,
            'employee_time_record_id' => $this->employee_time_record_id,
            'payroll_period_id' => $this->payroll_period_id,
            'month' => $this->month,
            'year' => $this->year,
            'gross_salary' => $this->gross_salary,
            'total_deductions' => $this->total_deductions,
            'total_receivables' => $this->total_receivables,
            'net_pay' => $this->net_pay,
        ];
    }
}