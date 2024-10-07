<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;

class DeductionStatusListResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $dateArray = request()->processMonth;

        // Construct the date strings from the array
        $dateToString = $dateArray['year'] . '-' . $dateArray['month'] . '-' . $dateArray['JOtoPeriod'];
        $dateFromString = $dateArray['year'] . '-' . $dateArray['month'] . '-' . $dateArray['JOfromPeriod'];

        // Create DateTime objects from the generated date strings
        $payrollDateTo = new DateTime($dateToString);
        $payrollDateFrom = new DateTime($dateFromString);

        // Check if JOtoPeriod is zero
        if ($dateArray['JOtoPeriod'] === 0) {
            // Set payrollDateTo to the last day of the month
            $payrollDateTo = (new DateTime("$dateArray[year]-$dateArray[month]-01"))->modify('last day of this month');
        }

        // Check if JOfromPeriod is zero
        if ($dateArray['JOfromPeriod'] === 0) {
            // Set payrollDateFrom to the first day of the month
            $payrollDateFrom = (new DateTime("$dateArray[year]-$dateArray[month]-01"));
        }
            
        $activeImports = $this->getImports()
        ->whereBetween('payroll_date', [$payrollDateFrom, $payrollDateTo])
        ->orderBy('payroll_date', 'desc') // Optional, if you want to order them
        ->get();
        return [
            'id' => $this->id,
            'deduction_group_id' => $this->deduction_group_id,
            'name' => $this->name,
            'code' => $this->code,
            'amount' => $this->amount !== null ? '₱' . $this->amount : $this->percentage . '%',
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'employment_type' => $this->employment_type,
            'is_active' => $this->is_active,
            'hasImport'=>count($activeImports) <= 0 ? false : true,
            'isRegularDeduction'=>$this->employment_type === "Job Order" ? false:true
            //  count($this->getImports) > 0 ? true:false,
        ];
    }
}
