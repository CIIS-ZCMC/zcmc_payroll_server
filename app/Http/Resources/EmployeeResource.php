<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_number' => $this->employee_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'extension_name' => $this->extension_name,
            'full_name' => trim($this->first_name.' '.($this->middle_name ? $this->middle_name.' ' : '').$this->last_name),
            'designation' => $this->designation,
            'hire_date' => $this->hire_date?->toDateString(),
            'is_newly_hired' => $this->is_newly_hired,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
