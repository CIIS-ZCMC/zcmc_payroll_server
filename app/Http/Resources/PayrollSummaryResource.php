<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payroll_run_id' => $this->payroll_run_id,
            'run' => new PayrollRunResource($this->whenLoaded('run')),
            'total_employees' => $this->total_employees,
            'total_gross' => $this->total_gross,
            'total_net' => $this->total_net,
            'total_deductions' => $this->total_deductions,
            'total_receivables' => $this->total_receivables,
            'total_overtime_pay' => $this->total_overtime_pay,
            'total_night_diff_pay' => $this->total_night_diff_pay,
            'total_adjustments' => $this->total_adjustments,
            'total_absences' => $this->total_absences,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
