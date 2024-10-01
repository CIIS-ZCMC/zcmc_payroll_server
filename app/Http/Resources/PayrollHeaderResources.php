<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserInformationResource;
use App\Http\Resources\withBenefitsResource;
use App\Helpers\Helpers;

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
            'employment_type'=>$this->employment_type,
            'period'=>$this->from.' - '.$this->to,
            'from'=>$this->fromPeriod,
            'to'=>$this->toPeriod,
            'days_of_duty'=>$this->days_of_duty,
            'created_by'=>UserInformationResource::collection([decrypt($this->created_by)]),
            'included'=>$this->genPayrolls,
            'benefits'=>withBenefitsResource::collection($this->genPayrolls),
            'is_special'=>$this->is_special,
            'is_locked'=>$this->is_locked,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
        ];
    }
}
