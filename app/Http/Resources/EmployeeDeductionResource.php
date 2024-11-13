<?php

namespace App\Http\Resources;

use App\Models\EmployeeDeductionAdjustment;
use App\Models\EmployeeDeductionTrail;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDeductionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $adjustment = EmployeeDeductionAdjustment::where('employee_deduction_id', $this->id)
            ->where('month', $request->processMonth['month'])
            ->where('year', $request->processMonth['year'])
            ->first();

        // Fetch outside payments marked as new
        $other_deductions = EmployeeDeductionTrail::where('employee_deduction_id', $this->id)->get();

        return [
            'id' => $this->id,
            'deduction_id' => $this->deduction_id,
            'deduction' => [
                'name' => $this->deductions->name ?? 'N/A',
                'code' => $this->deductions->code ?? 'N/A',
            ],
            'amount' => $this->willDeduct === null ? ($adjustment->amount ?? 0) : $this->amount,
            'percentage' => $this->percentage,
            'frequency' => $this->frequency,
            'total_term' => $this->total_term,
            'term_paid' => $this->employeeDeductionTrails->count(),
            'is_default' => $this->is_default,
            'status' => $this->status,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'stopped_at' => $this->stopped_at,
            'default_value' => $this->getDeductions,
            'updated_on' => $this->updated_at,
            'will_deduct' => $this->willDeduct,
            'other_deduction' => $other_deductions ? EmployeeDeductionTrailResource::collection($other_deductions) : [],
        ];
    }
}
