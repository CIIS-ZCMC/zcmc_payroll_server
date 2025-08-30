<?php

namespace App\Data;

use App\Models\DeductionGroup;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class DeductionGroupData extends Data
{

    public function __construct(
        public string $deduction_group_uuid,
        public string $name,
        public string $code,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['deduction_group_uuid'] ?? self::generateUuid(),
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

    protected static function generateUuid(): string
    {
        $lastCode = DeductionGroup::orderBy('id', 'desc')->value('deduction_group_uuid');

        if (!$lastCode) {
            return 'DG-00001';
        }

        $number = (int) substr($lastCode, 3);
        $nextNumber = $number + 1;

        return 'DG-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}