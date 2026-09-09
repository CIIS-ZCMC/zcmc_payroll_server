<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class PayrollProcessData extends Data
{

    public function __construct(
        public int $payroll_period_id,
        public int $payroll_type,
        public int $current_step,
        public string $status,
        public string $started_by,
        public string $started_at,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            (int) $request['payroll_period_id'],
            (int) $request['payroll_type'],
            (int) $request['current_step'],
            $request['status'],
            $request['started_by'],
            $request['started_at'] ?? now()->toDateTimeString(),
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