<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReceivableResource extends JsonResource
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
            'employee_list_id' => $this->employee_list_id,
            'name' => $this->name,
            'amount' => $this->amount,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        
        ];
    }
}
