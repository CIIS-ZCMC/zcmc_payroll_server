<?php

namespace App\Http\Controllers\GeneralPayroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helpers;
use App\Models\ExcludedEmployee;

use function PHPUnit\Framework\isNull;

class ComputationController extends Controller
{
    public function computeNightDifferentialAmount($employeeList, $monthly_rate, $tempNetSalary)
    {
        $nightHours = $employeeList->getTimeRecords->total_night_duty_hours;
        $nd_Rate = floor($monthly_rate * 0.005682 * 100) / 100;
        $nd_Twenty_Percent = number_format($nd_Rate * 0.2, 2, '.', '');
        $Accumulated_Amount_Night_Differential = number_format($nightHours * $nd_Twenty_Percent, 2, '.', '');
        return Helpers::customRound($tempNetSalary + $Accumulated_Amount_Night_Differential);
    }

    public function computeDeductionAmount($employeeList)
    {
        $perdeductions = $employeeList->getListOfDeductions()->with(['deductions'])->get();
        $totalDeductions = 0;
        foreach ($perdeductions as $deduction) {
            if ($deduction->stopped_at) {
                $totalDeductions = 0;
            } else if (!is_null($deduction->date_from) && !is_null($deduction->date_to)) {
                if (strtotime($deduction->date_from) <= strtotime(date('Y-m-d')) && strtotime($deduction->date_to) >= strtotime(date('Y-m-d'))) {
                    $totalDeductions += $deduction->amount;
                }
            } else {
                $totalDeductions += $deduction->amount;
            }
        }
        return Helpers::customRound($totalDeductions);
    }

    public function computeReceivableAmounts($employeeList)
    {
        $perReceivables = $employeeList->getEmployeeReceivables()->with(['receivables'])->get();
        $totalReceivalbles = 0;
        foreach ($perReceivables as $receivable) {
            if ($receivable->stopped_at) {
                $totalReceivalbles = 0;
            } else if (!is_null($receivable->date_from) && !is_null($receivable->date_to)) {
                if (strtotime($receivable->date_from) <= strtotime(date('Y-m-d')) && strtotime($receivable->date_to) >= strtotime(date('Y-m-d'))) {
                    $totalReceivalbles += $receivable->amount;
                }
            } else {
                $totalReceivalbles += $receivable->amount;
            }
        }
        return Helpers::customRound($totalReceivalbles);
    }

    public function computeTaxesAmounts($employeeList)
    {

        $employeeTaxes = $employeeList->getTaxes->filter(function ($row) {
            return $row->month == request()->processMonth['month'] &&
                $row->year == request()->processMonth['year'];
        });
        $totalTaxes = 0;
        if (count($employeeTaxes) >= 1) {
            foreach ($employeeTaxes as $value) {
                $totalTaxes += $value->with_holding_tax;
            }
        }
        return Helpers::customRound($totalTaxes);
    }

    public function checkOutofPayroll($data)
    {
        $year = $data['employee_list']->getTimeRecords->year;
        $month = $data['employee_list']->getTimeRecords->month;
        $CheckExcluded = ExcludedEmployee::where("employee_list_id", $data['employee_list']->id)
            ->where("year", $year)
            ->where("month", $month);
        $requiredAmount = 5000;

        if ($data['employment_type'] == "Job Order") {
            $requiredAmount = 2500;
        }

        if ($data['NETSalary'] < $requiredAmount) {
            if ($CheckExcluded->exists()) {
                //Update is removed to 0 . then update the amount and message
                if ($CheckExcluded->first()->is_removed) {
                    $CheckExcluded->update([
                        'reason' => json_encode([
                            'reason' => 'Salary Below 5000',
                            'remarks' => 'General payroll processed and output is below 5k',
                            'Amount' => $data['NETSalary'],
                        ]),
                        'year' => $year,
                        'month' => $month,
                        'is_removed' => 0
                    ]);
                }
            } else {
                //Create new Excluded..
                ExcludedEmployee::create([
                    'employee_list_id' => $data['employee_list']->id,
                    'payroll_headers_id' => $data['PayheaderID'] ?? null,
                    'reason' => json_encode([
                        'reason' => 'Salary Below 5000',
                        'remarks' => 'General payroll processed and output is below 5k',
                        'Amount' => $data['NETSalary'],
                    ]),
                    'year' => $year,
                    'month' => $month,
                    'is_removed' => 0
                ]);
            }

            return true;
        }
        return false;
    }

    public function divideintoTwo($number)
    {
        $firstHalf = floor($number / 2);

        $secondHalf = $number - $firstHalf;
        return [Helpers::customRound($firstHalf), Helpers::customRound($secondHalf)];
    }

    public function ComputeNetSalary($NetSalarywNightDifferential, $TotalReceivables, $TotalDeductions, $TotalTaxex)
    {
        return Helpers::customRound(($NetSalarywNightDifferential + $TotalReceivables) - ($TotalDeductions + $TotalTaxex));
    }
}
