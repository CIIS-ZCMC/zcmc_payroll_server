<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\EmployeeListResource;
use App\Models\EmployeeList;

class GeneralPayrollResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //  return parent::toArray($request);
        return [
            'id' => $this->id,
            'payroll_headers_id' => $this->payroll_headers_id,
            'employee_list_id' => $this->employee_list_id,
            'employee_list' => EmployeeListResource::collection([EmployeeList::find($this->employee_list_id)])->first(),
            'time_records' => json_decode($this->time_records),
            'employee_receivables' => json_decode($this->employee_receivables),
            'employee_deductions' => json_decode($this->employee_deductions),
            'employee_taxes' => json_decode($this->employee_taxes),
            'base_salary' => decrypt($this->base_salary),
            'net_pay' => decrypt($this->net_pay),
            'gross_pay' => decrypt($this->gross_pay),
            'net_salary_first_half' => $this->net_salary_first_half != 0 ? decrypt($this->net_salary_first_half) : 0,//decrypt($this->net_salary_first_half),
            'net_salary_second_half' => $this->net_salary_second_half != 0 ? decrypt($this->net_salary_second_half) : 0,//decrypt($this->net_salary_second_half),
            'net_total_salary' => decrypt($this->net_total_salary),
            'month' => $this->month,
            'year' => $this->year,
            'firstHalf' => firstPayrollResource::collection([$this->firstHalf])->first(),
            'secondHalf' => secondPayrollResource::collection([$this->secondHalf])->first(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
