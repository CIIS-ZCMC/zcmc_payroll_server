<?php

namespace App\Data;

use App\Models\DeductionGroup;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class DeductionGroupData extends Data
{

    public function __construct(
        public string $name,
        public string $code,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['name'],
            $request['code']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
        ];
    }
}
