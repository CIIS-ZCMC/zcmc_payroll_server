<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class LateDeductionMatrixData extends Data
{

    public function __construct(
        public string $employment_type,
        public int $from_minutes,
        public int $to_minutes,
        public float $amount,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['employment_type'],
            $request['from_minutes'],
            $request['to_minutes'],
            $request['amount'],
        );
    }

    public function toArray(): array
    {
        return [
            'employment_type' => $this->employment_type,
            'from_minutes' => $this->from_minutes,
            'to_minutes' => $this->to_minutes,
            'amount' => $this->amount,
        ];
    }
}
