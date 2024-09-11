<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListOfEmployeeByDeductionResource extends JsonResource
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
            'deduction_id' => $this->deduction_id,
            'deduction' => [
                'name' => $this->deductions->name ?? 'N/A',
                'code' => $this->deductions->code ?? 'N/A',
            ],
            'amount' => $this->amount,
            'percentage' => $this->percentage . '%',
            'frequency' => $this->frequency,
            'total_term' => $this->total_term,
            'is_default' => $this->is_default,
            'status' =>  $this->status,
            'date_from' =>  $this->date_from,
            'date_to' =>  $this->date_to,
            'stopped_at' =>  $this->stopped_at,
            'employeeDeductionList' => $this->employeeList,
            'isDifferential'=>$this->isDifferential,
            'total_paid'=>$this->total_paid
        ];
    }
}
