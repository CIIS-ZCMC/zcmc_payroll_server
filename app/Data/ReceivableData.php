<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class ReceivableData extends Data
{
    
    public function __construct(
        public string $receivable_uuid,
        public string $name,
        public string $code,
        public string $type,
        public bool $hasDate,
        public ?string $date_start,
        public ?string $date_end,
        public ?string $condition_operator,
        public ?float $condition_value,
        public ?float $percent_value,
        public ?float $fixed_amount,
        public string $billing_cycle,
        public string $status,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['receivable_uuid'],
            $request['name'],
            $request['code'],
            $request['type'],
            $request['hasDate'] ?? false,
            $request['date_start'] ?? null,
            $request['date_end'] ?? null,
            $request['condition_operator'] ?? null,
            $request['condition_value'] ?? null,
            $request['percent_value'] ?? null,
            $request['fixed_amount'] ?? null,
            $request['billing_cycle'],
            $request['status'],
        );
    }

    public function toArray(): array
    {
        return [
            'receivable_uuid' => $this->receivable_uuid,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'hasDate' => $this->hasDate,
            'date_start' => $this->date_start ?? null,
            'date_end' => $this->date_end ?? null,
            'condition_operator' => $this->condition_operator ?? null,
            'condition_value' => $this->condition_value ?? null,
            'percent_value' => $this->percent_value ?? null,
            'fixed_amount' => $this->fixed_amount ?? null,
            'billing_cycle' => $this->billing_cycle,
            'status' => $this->status,
        ];
    }
}