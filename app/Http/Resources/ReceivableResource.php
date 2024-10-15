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
            'name' => $this->name,
            'code' => $this->code,
            'employment_type' => $this->employment_type,
            'charge_basis' => $this->amount ? "Fixed Amount" : "Percentage Of Salary",
            'amount' => (string) ($this->percentage !== null ? $this->percentage : $this->amount),
            'billing_cycle' => $this->billing_cycle,
            'terms_to_pay' => $this->terms_to_pay,
            'is_applied_to_all' => $this->is_applied_to_all,
            'apply_salarygrade_from' => $this->apply_salarygrade_from,
            'apply_salarygrade_to' => $this->apply_salarygrade_to,
            'is_mandatory' => $this->is_mandatory ? "Yes" : "No",
            'status' => $this->status,
            'reason' => $this->reason ?? "",
            'created_at' => Carbon::parse($this->created_at)->format('M d, Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('M d, Y'),
            'stopped_at' => $this->stopped_at ? Carbon::parse($this->stopped_at)->format('M d, Y') : "N/A"
        ];
    }
}
