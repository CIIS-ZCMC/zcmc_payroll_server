<?php

namespace App\Data;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class EmployeeData extends Data
{
    public function __construct(
        public string $employee_profile_id,
        public string $employee_number,
        public string $first_name,
        public string $last_name,
        public ?string $middle_name,
        public ?string $ext_name,
        public string $designation,
        public ?array $assigned_area,
        public string $status,
        public bool $is_newly_hired,
        public bool $is_excluded,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request['employee_profile_id'],
            $request['employee_number'],
            $request['first_name'],
            $request['last_name'],
            $request['middle_name'] ?? null,
            $request['ext_name'] ?? null,
            $request['designation'],
            $request['assigned_area'] ?? null,
            $request['status'],
            $request['is_newly_hired'],
            $request['is_excluded']
        );
    }

    public function toArray(): array
    {
        return [
            'employee_profile_id' => $this->employee_profile_id,
            'employee_number' => $this->employee_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'ext_name' => $this->ext_name,
            'designation' => $this->designation,
            'assigned_area' => $this->assigned_area,
            'status' => $this->status,
            'is_newly_hired' => $this->is_newly_hired,
            'is_excluded' => $this->is_excluded,
        ];
    }
}