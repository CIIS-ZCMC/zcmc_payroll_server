<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class PayrollPeriodData extends Data
{
    
    public function __construct(
        public string $month,
        public string $year,
        public string $employment_type,
        public string $period_type,
        public string $period_start,
        public string $period_end,
        public int $days_of_duty,
        public bool $is_special,
        public ?string $posted_at,
        public ?string $last_generated_at,
        public ?string $locked_at,
        public bool $is_active,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
          return new self(
            $request['month'],
            $request['year'],
            $request['employment_type'],
            $request['period_type'],
            $request['period_start'],
            $request['period_end'],
            $request['days_of_duty'],
            $request['is_special'],
            $request['posted_at'] ?? null,
            $request['last_generated_at'] ?? null,
            $request['locked_at'] ?? null,
            $request['is_active'],
        );
    }

    public function toArray(): array
    {
        return [
            'month' => $this->month,
            'year' => $this->year,
            'employment_type' => $this->employment_type,
            'period_type' => $this->period_type,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'days_of_duty' => $this->days_of_duty,
            'is_special' => $this->is_special,
            'posted_at' => $this->posted_at ?? null,    
            'last_generated_at' => $this->last_generated_at ?? null,
            'locked_at' => $this->locked_at ?? null,
            'is_active' => $this->is_active,
        ];
    }
}