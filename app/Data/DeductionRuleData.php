<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class DeductionRuleData extends Data
{
    public function __construct(
        public int $deduction_id,
        public ?int $min_salary,
        public ?int $max_salary,
        public string $apply_type,
        public string $value,
        public ?string $effective_date,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['deduction_id'],
            $request['min_salary'] ?? null,
            $request['max_salary'] ?? null,
            $request['apply_type'],
            $request['value'],
            $request['effective_date'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'deduction_id' => $this->deduction_id,
            'min_salary' => $this->min_salary ?? null,
            'max_salary' => $this->max_salary ?? null,
            'apply_type' => $this->apply_type,
            'value' => $this->value,
            'effective_date' => $this->effective_date ?? null,
        ];
    }
}