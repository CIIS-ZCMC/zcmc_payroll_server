<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class EmployeeDeductionData extends Data
{
    public function __construct(
        public int $employee_id,
        public int $deduction_id,
        public ?int $payroll_period_id,
        public string $billing_cycle,
        public ?float $amount,
        public ?float $percentage,
        public ?string $effective_date,
        public ?string $end_date,
        public ?bool $is_default,
        public ?bool $is_active,
        public ?string $stopped_at,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['employee_id'],
            $data['deduction_id'],
            $data['payroll_period_id'] ?? null,
            $data['billing_cycle'] ?? 'monthly',
            $data['amount'] ?? null,
            $data['percentage'] ?? null,
            $data['effective_date'] ?? null,
            $data['end_date'] ?? null,
            $data['is_default'] ?? false,
            $data['is_active'] ?? false,
            $data['stopped_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employee_id,
            'deduction_id' => $this->deduction_id,
            'payroll_period_id' => $this->payroll_period_id,
            'billing_cycle' => $this->billing_cycle,
            'amount' => $this->amount ?? null,
            'percentage' => $this->percentage ?? null,
            'effective_date' => $this->effective_date ?? null,
            'end_date' => $this->end_date ?? null,
            'is_default' => $this->is_default ?? false,
            'is_active' => $this->is_active ?? false,
            'stopped_at' => $this->stopped_at ?? null,
        ];
    }
}
