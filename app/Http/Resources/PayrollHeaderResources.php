<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserInformationResource;

class PayrollHeaderResources extends JsonResource
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
            'month'=>$this->month,
            'year'=>$this->year,
            'created_by'=>UserInformationResource::collection([decrypt($this->created_by)]),
            'is_locked'=>$this->is_locked,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
        ];
    }
}