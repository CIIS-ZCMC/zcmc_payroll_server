<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class EmployeeReceivableData extends Data
{
    public function __construct(
        public int $employee_id,
        public int $receivable_id,
        public ?int $payroll_period_id,
        public string $billing_cycle,
        public ?float $amount,
        public ?int $percentage,
        public ?string $effective_date,
        public ?string $end_date,
        public ?bool $is_active,
        public bool $is_default,
        public ?string $remarks,
        public ?string $stopped_at,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['employee_id'],
            $data['receivable_id'],
            $data['payroll_period_id'],
            $data['billing_cycle'],
            $data['amount'] ?? null,
            $data['percentage'] ?? null,
            $data['effective_date'] ?? null,
            $data['end_date'] ?? null,
            $data['is_active'] ?? null,
            $data['is_default'],
            $data['remarks'] ?? null,
            $data['stopped_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'payroll_period_id' => $this->payroll_period_id,
            'employee_id' => $this->employee_id,
            'receivable_id' => $this->receivable_id,
            'billing_cycle' => $this->billing_cycle,
            'amount' => $this->amount ?? null,
            'percentage' => $this->percentage ?? null,
            'effective_date' => $this->effective_date ?? null,
            'end_date' => $this->end_date ?? null,
            'is_active' => $this->is_active ?? null,
            'is_default' => $this->is_default,
            'remarks' => $this->remarks ?? null,
            'stopped_at' => $this->stopped_at ?? null,
        ];
    }
}
