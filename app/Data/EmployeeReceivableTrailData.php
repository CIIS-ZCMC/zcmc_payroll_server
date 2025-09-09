<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class EmployeeReceivableTrailData extends Data
{
    public function __construct(
        public int $employee_receivable_id,
        public int $total_term,
        public int $total_term_paid,
        public float $amount_paid,
        public string $date_paid,
        public float $balance,
        public string $status,
        public string $remarks,
        public bool $is_last_payment,
        public bool $is_adjustment,

    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['employee_receivable_id'],
            $request['total_term'],
            $request['total_term_paid'],
            $request['amount_paid'],
            $request['date_paid'],
            $request['balance'],
            $request['status'],
            $request['remarks'],
            $request['is_last_payment'],
            $request['is_adjustment'],
        );
    }

    public function toArray(): array
    {
        return [
            'employee_receivable_id' => $this->employee_receivable_id,
            'total_term' => $this->total_term,
            'total_term_paid' => $this->total_term_paid,
            'amount_paid' => $this->amount_paid,
            'date_paid' => $this->date_paid,
            'balance' => $this->balance,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'is_last_payment' => $this->is_last_payment,
            'is_adjustment' => $this->is_adjustment,
        ];
    }
}