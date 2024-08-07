<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDeductionResource extends JsonResource
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
            'amount' => $this->amount,
            'percentage' => $this->percentage,
            'frequency' => $this->frequency,
            'total_term' => $this->total_term,
            'is_default' => $this->is_default,
            'deduction' => [
                'name' => $this->deduction->name ?? 'N/A',
                'code' => $this->deduction->code ?? 'N/A',
            ],
        ];
    }
}
