<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class EmployeeDeductionData extends Data
{
    public function __construct(
        public string $payroll_period_id,
        public string $employee_id,
        public string $deduction_id,
        public string $frequency,
        public ?float $amount,
        public ?float $percentage,
        public ?string $date_from,
        public ?string $date_to,
        public bool $with_terms,
        public ?int $total_term,
        public ?int $total_paid,
        public bool $is_default,
        public ?string $isDifferential,
        public ?string $reason,
        public ?string $status,
        public ?string $willDeduct,
        public ?string $stopped_at,
        public ?string $completed_at,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['payroll_period_id'],
            $request['employee_id'],
            $request['deduction_id'],
            $request['frequency'],
            $request['amount'] ?? null,
            $request['percentage'] ?? null,
            $request['date_from'] ?? null,
            $request['date_to'] ?? null,
            $request['with_terms'],
            $request['total_term'] ?? null,
            $request['total_paid'] ?? null,
            $request['is_default'],
            $request['isDifferential'] ?? null,
            $request['reason'] ?? null,
            $request['status'] ?? null,
            $request['willDeduct'] ?? null,
            $request['stopped_at'] ?? null,
            $request['completed_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'payroll_period_id' => $this->payroll_period_id,
            'employee_id' => $this->employee_id,
            'deduction_id' => $this->deduction_id,
            'frequency' => $this->frequency,
            'amount' => $this->amount ?? null,
            'percentage' => $this->percentage ?? null,
            'date_from' => $this->date_from ?? null,
            'date_to' => $this->date_to ?? null,
            'with_terms' => $this->with_terms,
            'total_term' => $this->total_term ?? null,
            'total_paid' => $this->total_paid ?? null,
            'is_default' => $this->is_default,
            'isDifferential' => $this->isDifferential ?? null,
            'reason' => $this->reason ?? null,
            'status' => $this->status ?? null,
            'willDeduct' => $this->willDeduct ?? null,
            'stopped_at' => $this->stopped_at ?? null,
            'completed_at' => $this->completed_at ?? null,
        ];
    }
}