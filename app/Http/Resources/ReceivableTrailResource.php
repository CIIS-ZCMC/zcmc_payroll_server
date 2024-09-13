<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceivableTrailResource extends JsonResource
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
            'receivable_id' => $this->receivables->id,
            'receivable_name' => $this->receivables->name,
            'status' => $this->status,
            'date_from' => Carbon::parse($this->from)->format('M d, Y'),
            'date_to' => Carbon::parse($this->to)->format('M d, Y'),
            'reason' => $this->reason,
            'created_at' => Carbon::parse($this->created_at)->format('M d, Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('M d, Y'),
        ];
    }
}
