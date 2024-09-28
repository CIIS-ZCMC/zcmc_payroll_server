<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class withBenefitsResource extends JsonResource
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
            'empID'=>$this->employee_list_id ,  
            'default'=>$receivables = array_filter(json_decode($this->employee_receivables), function ($row) {
                        return $row->receivable_id == null && $row->receivable->code == "HAZARD"  && $row->amount <=0;
                    }),
             'other'=>$receivables = array_filter(json_decode($this->employee_receivables), function ($row) {
                        return isset($row->receivable_id) && $row->receivable_id !== null;
                    }) 
        ];
        // return [
        //     'default'=>$receivables = array_filter(json_decode($this->employee_receivables), function ($row) {
        //         return $row->receivable_id == null && $row->receivable->code == "HAZARD"  && $row->amount <=0;
        //     }),
        //     'other'=>$receivables = array_filter(json_decode($this->employee_receivables), function ($row) {
        //         return isset($row->receivable_id) && $row->receivable_id !== null;
        //     })
        // ];
     
        
    }
}
