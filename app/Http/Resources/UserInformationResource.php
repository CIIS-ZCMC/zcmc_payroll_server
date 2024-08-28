<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'employee_profile_id' => $this['employee_profile_id'],
            'employeeID'=>$this['employee_id'],
            'name'=>$this['name'],
            'designation'=>$this['designation'],
        ];
    }
}
