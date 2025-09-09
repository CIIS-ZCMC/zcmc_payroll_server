<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class GeneralPayrollData extends Data
{
    public function __construct(
        public string $generated_by_id,
        public string $generated_by_name,
        public int $payroll_period_id,
        public int $total_employees,
        public float $total_deductions,
        public float $total_receivables,
        public float $total_gross,
        public float $total_net,
        public string $month,
        public string $year,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['generated_by_id'],
            $request['generated_by_name'],
            $request['payroll_period_id'],
            $request['total_employees'],
            $request['total_deductions'],
            $request['total_receivables'],
            $request['total_gross'],
            $request['total_net'],
            $request['month'],
            $request['year'],
        );
    }

    public function toArray(): array
    {
        return [
            'generated_by_id' => $this->generated_by_id,
            'generated_by_name' => $this->generated_by_name,
            'payroll_period_id' => $this->payroll_period_id,
            'total_employees' => $this->total_employees,
            'total_deductions' => $this->total_deductions,
            'total_receivables' => $this->total_receivables,
            'total_gross' => $this->total_gross,
            'total_net' => $this->total_net,
            'month' => $this->month,
            'year' => $this->year,
        ];
    }
}