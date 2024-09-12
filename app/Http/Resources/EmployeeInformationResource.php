<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\EmployeeSalaryResource;
use App\Http\Resources\TimeRecordResource;

class EmployeeInformationResource extends JsonResource
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
            'id'=>$this->id,
            'employee_number'=>$this->employee_number,
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'middle_name'=>$this->middle_name,
            'designation'=>$this->designation,
            'created'=>$this->created_at,
            'assigned_area'=>json_decode($this->assigned_area),
            'status'=>$this->status,
            'is_newly_hired'=>$this->is_newly_hired,
            'Salary'=> EmployeeSalaryResource::collection([$this->getSalary]),
            'TimeRecord'=> TimeRecordResource::collection([$this->getTimeRecords]),
            'Deduction'=>$this->getListOfDeductions,
            'Receivables'=>$this->getEmployeeReceivables,
            'isExcluded'=>$this->getExclusionDetails()->where('month', request()->processMonth['month'])
                ->where('year',request()->processMonth['year'])
            ->get()
        ];
    }
}
