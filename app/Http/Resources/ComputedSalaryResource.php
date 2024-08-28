<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ComputedSalaryResource extends JsonResource
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
            'time_record_id' => $this->time_record_id,
            'computed_salary' => $this->computed_salary, // Decrypted or processed as needed
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
