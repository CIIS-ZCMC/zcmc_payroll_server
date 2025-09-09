<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class EmployeeAdjustmentData extends Data
{
    public function __construct(
        public string $action_by,
        public int $payroll_period_id,
        public ?int $employee_deduction_id = null,
        public ?int $employee_receivable_id = null,
        public float $amount,
        public float $amount_to_pay,
        public float $amount_balance,
        public string $reason,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $user = [
            'employee_id' => $request['user']->employee_id,
            'employee_name' => $request['user']->name,
        ];

        return new self(
            json_encode($user),
            $request['payroll_period_id'],
            $request['employee_deduction_id'] ?? null,
            $request['employee_receivable_id'] ?? null,
            $request['amount'],
            $request['amount_to_pay'],
            $request['amount_balance'],
            $request['reason']
        );
    }

    public function toArray(): array
    {
        return [
            'action_by' => $this->action_by,
            'payroll_period_id' => $this->payroll_period_id,
            'employee_deduction_id' => $this->employee_deduction_id ?? null,
            'employee_receivable_id' => $this->employee_receivable_id ?? null,
            'amount' => $this->amount,
            'amount_to_pay' => $this->amount_to_pay,
            'amount_balance' => $this->amount_balance,
            'reason' => $this->reason,
        ];
    }
}