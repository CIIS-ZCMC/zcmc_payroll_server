<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'uuid' => $this->receivable_uuid,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'billing_cycle' => $this->billing_cycle,
            'percent_value' => $this->percent_value ?? 0,
            'fixed_amount' => $this->fixed_amount ?? 0,
            'status' => $this->status,
            'receivable_rule' => ReceivableRuleResource::collection($this->whenLoaded('receivableRule')),
            'deleted_at' => Carbon::parse($this->deleted_at)->format('M d, Y'),
            'created_at' => Carbon::parse($this->created_at)->format('M d, Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('M d, Y'),
        ];
    }
}
