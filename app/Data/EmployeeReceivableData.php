<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class EmployeeReceivableData extends Data
{
    public function __construct(
        public string $payroll_period_id,
        public string $employee_id,
        public string $receivable_id,
        public string $frequency,
        public ?float $amount,
        public ?float $percentage,
        public ?string $date_from,
        public ?string $date_to,
        public ?int $total_paid,
        public bool $is_default,
        public ?string $reason,
        public ?string $status,
        public ?string $stopped_at,
        public ?string $completed_at,
    ) {
    }

    public static function fromRequest(array $request): self
    {
        return new self(
            $request['payroll_period_id'],
            $request['employee_id'],
            $request['receivable_id'],
            $request['frequency'],
            $request['amount'] ?? null,
            $request['percentage'] ?? null,
            $request['date_from'] ?? null,
            $request['date_to'] ?? null,
            $request['total_paid'] ?? null,
            $request['reason'] ?? null,
            $request['status'] ?? null,
            $request['is_default'],
            $request['stopped_at'] ?? null,
            $request['completed_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'payroll_period_id' => $this->payroll_period_id,
            'employee_id' => $this->employee_id,
            'receivable_id' => $this->receivable_id,
            'frequency' => $this->frequency,
            'amount' => $this->amount ?? null,
            'percentage' => $this->percentage ?? null,
            'date_from' => $this->date_from ?? null,
            'date_to' => $this->date_to ?? null,
            'total_paid' => $this->total_paid ?? null,
            'reason' => $this->reason ?? null,
            'status' => $this->status ?? null,
            'is_default' => $this->is_default,
            'stopped_at' => $this->stopped_at ?? null,
            'completed_at' => $this->completed_at ?? null,
        ];
    }
}