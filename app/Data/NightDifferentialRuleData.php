<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class NightDifferentialRuleData extends Data
{
    public function __construct(
        public string $employment_type,
        public float $start_time,
        public float $end_time,
        public int $rate_percent,
        public string $effective_date
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['employment_type'],
            $request['start_time'],
            $request['end_time'],
            $request['rate_percent'],
            $request['effective_date'],
        );
    }

    public function toArray(): array
    {
        return [
            'employment_type' => $this->employment_type,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'rate_percent' => $this->rate_percent,
            'effective_date' => $this->effective_date
        ];
    }
}