<?php

namespace App\Imports;

use App\Models\Deduction;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\PayrollPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportEmployeeDeduction implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        try {

            $code = $collection[0][1]; // e.g. "WTAX"
            $monthName = $collection[1][1]; // e.g. "February"
            $year = $collection[1][2]; // e.g. 2024

            $month = date('n', strtotime($monthName));

            $deduction = Deduction::where('code', $code)->first();
            if (!$deduction) {
                throw new \Exception("Deduction code not found: $code");
            }

            $payroll_period = PayrollPeriod::where('employment_type', 'permanent')
                ->where('month', $month)
                ->where('year', $year)
                ->latest()
                ->first();

            if (!$payroll_period) {
                throw new \Exception("Payroll period not found for $monthName $year");
            }

            $data_rows = $collection->slice(2);
            foreach ($data_rows as $index => $row) {
                $employee = Employee::where('employee_number', $row[1])->first();
                if (!$employee) {
                    Log::warning("Employee not found: $row[1] on row " . ($index + 1));
                    continue; // Skip invalid row
                }

                $is_default = $row[3] == $deduction->fixed_amount ? 1 : 0;
                $employee_deduction_data = [
                    'payroll_period_id' => $payroll_period->id,
                    'employee_id' => $employee->id,
                    'deduction_id' => $deduction->id,
                    'amount' => $row[3],
                    'frequency' => 'monthly',
                    'with_terms' => 0,
                    'is_default' => $is_default,
                ];

                $employee_deduction = EmployeeDeduction::where([
                    'payroll_period_id' => $payroll_period->id,
                    'employee_id' => $employee->id,
                    'deduction_id' => $deduction->id,
                ])->first();

                if ($employee_deduction !== null) {
                    $employee_deduction->update($employee_deduction_data);
                } else {
                    EmployeeDeduction::create($employee_deduction_data);
                }
            }

        } catch (\Throwable $e) {
            Log::error('Import Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
