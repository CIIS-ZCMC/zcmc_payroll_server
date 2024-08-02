<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeductionResource extends JsonResource
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
            'deduction_group_id' => $this->deduction_group_id,
            'name' => $this->name,
            'code' => $this->code,
            'amount' => $this->amount,
            'percentage' => $this->percentage,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'employment_type' => $this->employment_type,
            'is_mandatory' => $this->is_mandatory,
            'is_active' => $this->is_active,
        ];
    }
}
