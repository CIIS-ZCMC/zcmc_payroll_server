<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class DeductionGroupData extends Data
{
    
    public function __construct(
        public string $deduction_group_uuid,
        public string $name,
        public string $code,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['deduction_group_uuid'],
            $request['name'],
            $request['code']
        );
    }

    public function toArray(): array
    {
        return [
            'deduction_group_uuid' => $this->deduction_group_uuid,
            'name' => $this->name,
            'code' => $this->code,
        ];
    }
}