<?php

namespace App\Http\Controllers\GeneralPayroll;

use App\Http\Controllers\Controller;
use App\Models\Deduction;
use App\Models\EmployeeDeduction;
use App\Models\Receivable;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\Helpers;
use App\Models\ExcludedEmployee;

use function PHPUnit\Framework\isNull;

class ComputationController extends Controller
{
    public function computeNightDifferentialAmount($employeeList, $monthly_rate, $tempNetSalary)
    {
        $nightHours = $employeeList->getTimeRecords->total_night_duty_hours ?? 0;
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

    public function computeTotalDeductionAmount($employee_list)
    {
        $deductions = EmployeeDeduction::where('employee_list_id', $employee_list->id)->where('status', 'Active')->get();
        $total_deduction = 0; // Initialize total deduction

        foreach ($deductions as $key => $deduction) {
            // Calculate deduction amount based on logic (example using amount)
            if ($deduction->willDeduct) { // Check if this deduction should be applied
                $deduction_amount = $deduction->amount;

                // If deduction is based on percentage
                if ($deduction->percentage) {
                    $deduction_amount = ($deduction->amount * $deduction->percentage) / 100;
                }

                // Add the deduction amount to the total
                $total_deduction += $deduction_amount;
            }
        }

        return Helpers::customRound($total_deduction);
    }

    public function computeReceivableAmounts($employeeList)
    {
        $perReceivables = $employeeList->getEmployeeReceivables()->with(['receivables'])->get();
        $totalReceivables = 0;

        foreach ($perReceivables as $receivable) {
            // Skip if stopped_at is set
            if ($receivable->stopped_at) {
                continue; // Skip this receivable, but do not reset total
            }

            // Check if there is a valid date range (both date_from and date_to are set)
            if (!is_null($receivable->date_from) && !is_null($receivable->date_to)) {
                $dateFrom = Carbon::parse($receivable->date_from);
                $dateTo = Carbon::parse($receivable->date_to);
                $today = Carbon::today();

                // Check if today's date is within the range
                if ($today->between($dateFrom, $dateTo)) {
                    $totalReceivables += $receivable->amount;
                }
            } else {
                // If no date range is provided, simply add the amount
                $totalReceivables += $receivable->amount;
            }
        }
        return Helpers::customRound($totalReceivables);
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

    public function ComputeNetSalary($EmployeeList, $NetSalary, $ReceivableAmount, $DeductionAmount)
    {
        $TimeRecords = $EmployeeList->getTimeRecords;
        $SalaryInfo = $EmployeeList->getSalary;

        // Ensure default values if records are null
        $TotalPresentDays = $TimeRecords->total_present_days ?? 0;
        $TotalAbsences = $TimeRecords->total_absences ?? 0;

        // Decrypt base salary
        $BaseSalary = decrypt($SalaryInfo->basic_salary);

        // Get other salary details
        $EmploymentType = $SalaryInfo->employment_type;
        $SalaryGrade = $SalaryInfo->salary_grade;

        // Calculate PERA and HAZARD pay
        $PERA = $this->CalculatePERA($TotalPresentDays, $TotalAbsences, $BaseSalary, $EmploymentType);
        $HAZARD = $this->CalculateHAZARDPay($SalaryGrade, $BaseSalary, $TotalAbsences);

        $TotalReceivables = $PERA + $HAZARD + $ReceivableAmount;
        $TotalGrossSalary = $NetSalary + $TotalReceivables;
        $TotalNetSalary = $TotalGrossSalary - $DeductionAmount;

        return Helpers::customRound($TotalNetSalary);
    }

    public function CalculatePERA($totalPresentDays, $totalAbsences, $baseSalary, $employmentType)
    {
        $pera = 2000;

        if ($employmentType === "Permanent Part-time") {
            if ($totalAbsences >= 1) {
                $salaryDedAbsent = floor((22 - $totalAbsences) / 22 * $baseSalary * 100) / 100;
                $totalDedForAbsent = floor(1000 / 22 * $totalAbsences * 100) / 100;
                $pera = floor((1000 - $totalDedForAbsent) * 100) / 100;
            }
        } else {
            if ($totalAbsences >= 1) {
                $salaryDedAbsent = floor((22 - $totalAbsences) / 22 * $baseSalary * 100) / 100;
                $totalDedForAbsent = floor(2000 / 22 * $totalAbsences * 100) / 100;
                $pera = floor((2000 - $totalDedForAbsent) * 100) / 100;
            }
        }

        return $pera;
    }

    public function CalculateHAZARDPay($salaryGrade, $basicSalary, $absences)
    {
        $monthlySalary = number_format($basicSalary, 2); // Formatting if needed
        $salaryPercentage = 0.0;

        if ($salaryGrade <= 19) {
            $salaryPercentage = 0.25;
        } elseif ($salaryGrade == 20) {
            $salaryPercentage = 0.15;
        } elseif ($salaryGrade == 21) {
            $salaryPercentage = 0.13;
        } elseif ($salaryGrade == 22) {
            $salaryPercentage = 0.12;
        } elseif ($salaryGrade == 23) {
            $salaryPercentage = 0.11;
        } elseif (in_array($salaryGrade, [24, 25])) {
            $salaryPercentage = 0.10;
        } elseif ($salaryGrade == 26) {
            $salaryPercentage = 0.09;
        } elseif ($salaryGrade == 27) {
            $salaryPercentage = 0.08;
        } elseif ($salaryGrade == 28) {
            $salaryPercentage = 0.07;
        } elseif (in_array($salaryGrade, [29, 30])) {
            $salaryPercentage = 0.06;
        } elseif ($salaryGrade == 31) {
            $salaryPercentage = 0.05;
        }

        if ($absences <= 11) {
            return (double) ($basicSalary * $salaryPercentage);
        }

        return 0.00;
    }

    public function hazardPayComputation($salaryGrade, $basicSalary, $workingDays)
    {
        $salaryPercentage = 0.0;

        if ($salaryGrade <= 19) {
            $salaryPercentage = 0.25;
        } elseif ($salaryGrade == 20) {
            $salaryPercentage = 0.15;
        } elseif ($salaryGrade == 21) {
            $salaryPercentage = 0.13;
        } elseif ($salaryGrade == 22) {
            $salaryPercentage = 0.12;
        } elseif ($salaryGrade == 23) {
            $salaryPercentage = 0.11;
        } elseif (in_array($salaryGrade, [24, 25])) {
            $salaryPercentage = 0.10;
        } elseif ($salaryGrade == 26) {
            $salaryPercentage = 0.09;
        } elseif ($salaryGrade == 27) {
            $salaryPercentage = 0.08;
        } elseif ($salaryGrade == 28) {
            $salaryPercentage = 0.07;
        } elseif (in_array($salaryGrade, [29, 30])) {
            $salaryPercentage = 0.06;
        } elseif ($salaryGrade == 31) {
            $salaryPercentage = 0.05;
        }

        if ($workingDays >= 12) {
            return (double) ($basicSalary * $salaryPercentage);
        } else if ($workingDays <= 11) {
            return (double) ($basicSalary * 0.14);
        } else if ($workingDays <= 6) {
            return (double) ($basicSalary * 0.8);
        } else {
            return 0.00;
        }
    }

    public function CalculateNightDifferential($totalNightDutyHours, $monthlyRate)
    {
        $totalAccumulatedND = 0.00;
        $nightdiffRate = floor($monthlyRate * 0.005682 * 100) / 100;
        $nightDifferentialTwentyPercentRate = floor($nightdiffRate * 0.2 * 100) / 100;
        $totalAccumulatedND = floor($totalNightDutyHours * $nightDifferentialTwentyPercentRate * 100) / 100;

        return $totalAccumulatedND;
    }

    public function calculateLeaveWithoutPay($requiredDaysOfDuty, $numberOfAbsent, $basicSalary)
    {
        return (($requiredDaysOfDuty - $numberOfAbsent) / $requiredDaysOfDuty) * $basicSalary;
    }
}
