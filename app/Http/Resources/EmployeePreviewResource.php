<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeePreviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $employee = $this['employee'];
        $payroll = $this['payroll'];

        $employee_name = $employee->last_name . ', ' . $employee->first_name . ' ' . ($employee->middle_name ? strtoupper(substr($employee->middle_name, 0, 1)) . '.' : '');
        $area = json_decode($employee->assigned_area ?? '{}', true) ?? [];
        $time = $employee->employeeTimeRecords->first();

        return [
            'id' => $employee->id,
            'employee_number' => $employee->employee_number,
            'full_name' => $employee_name,
            'designation' => $employee->designation,
            'assigned_area' => [
                'details' => [
                    'id' => $area['details']['id'] ?? null,
                    'name' => $area['details']['name'] ?? null,
                    'code' => $area['details']['code'] ?? null,
                ],
                'sector' => $area['sector'] ?? null
            ],
            'reason' => $employee->excludedEmployees->reason ?? 'Salary Below Threshold',
            'status' => $time?->status,
            'payroll' => [
                'payroll_period_id' => $payroll['payroll_period_id'],
                'employee_time_record_id' => $payroll['employee_time_record_id'],
                'total_receivables' => $payroll['total_receivables'],
                'total_deductions' => $payroll['total_deductions'],
                'basic_pay' => $payroll['basic_pay'],
                'gross_pay' => $payroll['gross_pay'],
                'net_pay' => $payroll['net_pay'],
                'first_half' => $payroll['first_half'],
                'second_half' => $payroll['second_half'],
                'currency' => 'PHP',
            ],
        ];
    }
}
