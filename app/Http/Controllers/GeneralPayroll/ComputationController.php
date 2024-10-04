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

    public function computeDeductionAmount($deductionSelected, $deductions, $isPermanent, $undertimeRate, $withoutPayAbsencesRate)
    {

        $restructedDeductions = $deductions->map(function ($row) {
            return [
                "deduction_id" => $row->deductions->id,
                "deduction" => [
                    "name" => $row->deductions->name,
                    "code" => $row->deductions->code
                ],
                "amount" => $row->amount,
            ];
        });

        if ($isPermanent) {
            $deductions = array_merge(
                [$withoutPayAbsencesRate],
                $restructedDeductions->toArray()
            );
        } else {
            $deductions = array_merge(
                [$undertimeRate, $withoutPayAbsencesRate],
                $restructedDeductions->toArray()
            );
        }


        if (isset($deductionSelected) && count($deductionSelected) >= 1) {
            $deductionIDs = array_map(function ($row) {
                return $row['id'];
            }, $deductionSelected);

            $deductions = array_values(array_filter($deductions, function ($item) use ($deductionIDs) {
                return $item['deduction_id'] === null || in_array($item['deduction_id'], $deductionIDs);
            }));

            if (!$isPermanent) {
                if (request()->processMonth['JOtoPeriod'] !== "15") {
                    $deductions = array_values(array_filter($deductions, function ($item) use ($deductionIDs) {
                        return $item['deduction_id'] === null;
                    }));
                }
            }
        }

        $totalDeduction = 0.00;
        foreach ($deductions as $row) {
            $totalDeduction += $row['amount'];
        }


        return [
            'totaldeduction' => $totalDeduction,
            'deductions' => $deductions
        ];

    }

    public function computeDeductionAmountBelow5k($employeeList)
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

    public function CalculatePERA($totalPresentDays, $totalAbsences, $baseSalary, $employmentType)
    {
        $pera = 2000;

        if ($employmentType === "Permanent Part-time") {
            if ($totalAbsences >= 1) {
                $salaryDedAbsent = floor((22 - $totalAbsences) / 22 * $baseSalary * 100) / 100;
                $totalDedForAbsent = floor(1000 / 22 * $totalAbsences * 100) / 100;
                $pera = floor((1000 - $totalDedForAbsent) * 100) / 100;
            } else {
                $pera = floor($totalPresentDays * 1000 / 22 * 100) / 100;
            }
        } else {
            if ($totalAbsences >= 1) {
                $salaryDedAbsent = floor((22 - $totalAbsences) / 22 * $baseSalary * 100) / 100;
                $totalDedForAbsent = floor(2000 / 22 * $totalAbsences * 100) / 100;
                $pera = floor((2000 - $totalDedForAbsent) * 100) / 100;
            } else {
                $pera = floor($totalPresentDays * 2000 / 22 * 100) / 100;
            }
        }

        return $pera;
    }

    public function CalculateHAZARDPay($salaryGrade, $basicSalary, $absences)
    {
        $monthlySalary = number_format($basicSalary, 2); // Formatting if needed
        $salaryPercentage = 0.0;


        switch (true) {
            case $salaryGrade <= 19:
                $salaryPercentage = 0.25;
                break;
            case $salaryGrade == 20:
                $salaryPercentage = 0.15;
                break;
            case $salaryGrade == 21:
                $salaryPercentage = 0.13;
                break;
            case $salaryGrade == 22:
                $salaryPercentage = 0.12;
                break;
            case $salaryGrade == 23:
                $salaryPercentage = 0.11;
                break;
            case in_array($salaryGrade, [24, 25]):
                $salaryPercentage = 0.1;
                break;
            case $salaryGrade == 26:
                $salaryPercentage = 0.09;
                break;
            case $salaryGrade == 27:
                $salaryPercentage = 0.08;
                break;
            case $salaryGrade == 28:
                $salaryPercentage = 0.07;
                break;
            case in_array($salaryGrade, [29, 30]):
                $salaryPercentage = 0.06;
                break;
            case $salaryGrade == 31:
                $salaryPercentage = 0.05;
                break;
            default:
                $salaryPercentage = 0;
                break;
        }



        if ($absences <= 11) {
            return (double) ($salaryPercentage * $basicSalary);
        }

        return 0.00;
    }

    public function CalculateNightDifferential($totalNightDutyHours, $monthlyRate)
    {
        $totalAccumulatedND = 0.00;
        $nightdiffRate = floor($monthlyRate * 0.005682 * 100) / 100;
        $nightDifferentialTwentyPercentRate = floor($nightdiffRate * 0.2 * 100) / 100;
        $totalAccumulatedND = floor($totalNightDutyHours * $nightDifferentialTwentyPercentRate * 100) / 100;

        return $totalAccumulatedND;
    }



}
