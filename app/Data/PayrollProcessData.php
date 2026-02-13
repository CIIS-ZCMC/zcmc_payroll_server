<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class PayrollProcessData extends Data
{

    public function __construct(
        public string $payroll_period_id,
        public string $payroll_type,
        public string $current_step,
        public string $status,
        public string $started_by,
        public string $started_at,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['payroll_period_id'],
            $request['payroll_type'],
            $request['current_step'],
            $request['status'],
            $request['started_by'],
            $request['started_at'],
        );
    }

    public function toArray(): array
    {
        return [
            'payroll_period_id' => $this->payroll_period_id,
            'payroll_type' => $this->payroll_type,
            'current_step' => $this->current_step,
            'status' => $this->status,
            'started_by' => $this->started_by,
            'started_at' => $this->started_at,
        ];
    }
}