<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExcludedEmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'employee_list_id' => EmployeeListResource::collection([$this->employeeList]),
            'payroll_headers_id ' => PayrollHeaderResources::collection([$this->payrollHeader]),
            'reason' => $this->last_name,
            'year' => $this->middle_name,
            'month' => $this->designation,
            'created' => $this->created_at
        ];
    }
}
