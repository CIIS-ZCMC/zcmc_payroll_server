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
        public int $month,
        public int $year,
        public float $basic_pay,
        public float $total_receivables,
        public float $gross_pay,
        public float $total_deductions,
        public float $net_pay,
    ) {
    }

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employee_id,
            'employee_time_record_id' => $this->employee_time_record_id,
            'payroll_period_id' => $this->payroll_period_id,
            'month' => $this->month,
            'year' => $this->year,
            'basic_pay' => $this->basic_pay,
            'total_receivables' => $this->total_receivables,
            'gross_pay' => $this->gross_pay,
            'total_deductions' => $this->total_deductions,
            'net_pay' => $this->net_pay,
        ];
    }
}