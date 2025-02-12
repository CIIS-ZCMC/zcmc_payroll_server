<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeductionGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $deductions = [];
        foreach ($this->deductions as $deduction) {
            $deductions[] = [
                "id" => $deduction->id,
                "name" => $deduction->name,
                "code" => $deduction->code,
            ];
        }

        return [
            "id" => $this->id,
            "name" => $this->name,
            "code" => $this->code,
            "deduction_type" => $deductions ?? []
        ];
    }
}
