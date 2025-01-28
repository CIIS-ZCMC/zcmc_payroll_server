<?php

namespace App\Http\Controllers\UMIS;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\EmployeeList;
use App\Models\UMIS\EmployeeProfile;
use App\Models\UMIS\InActiveEmployee;
use App\Models\UMIS\LeaveType;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EmployeeProfileController extends Controller
{
    public function index(Request $request)
    {
        $year_of = 2024;
        $month_of = 7;

        $totalDaysInMonth = Carbon::createFromDate($year_of, $month_of, 1)->daysInMonth;
        $expectedMinutesPerDay = 480;

        $helper = new Helpers();

        $employee_data = EmployeeProfile::with([
            'dailyTimeRecords' => function ($query) use ($year_of, $month_of) {
                $query->whereYear('dtr_date', $year_of)
                    ->whereMonth('dtr_date', $month_of)
                    ->selectRaw('biometric_id, SUM(total_working_minutes) as total_working_minutes, 
                                               SUM(overtime_minutes) as total_overtime_minutes,
                                               SUM(undertime_minutes) as total_undertime_minutes')
                    ->groupBy('biometric_id');
            },
            'approvedOB' => function ($query) use ($year_of, $month_of, $expectedMinutesPerDay) {
                $query->whereYear('date_from', $year_of)
                    ->whereMonth('date_from', $month_of)
                    ->where('status', 'approved')
                    ->selectRaw('employee_profile_id, SUM(DATEDIFF(date_to, date_from) + 1) * ? as total_ob_minutes', [$expectedMinutesPerDay])
                    ->groupBy('employee_profile_id');
            },
            'approvedOT' => function ($query) use ($year_of, $month_of, $expectedMinutesPerDay) {
                $query->whereYear('date_from', $year_of)
                    ->whereMonth('date_from', $month_of)
                    ->where('status', 'approved')
                    ->selectRaw('employee_profile_id, SUM(DATEDIFF(date_to, date_from) + 1) * ? as total_ot_minutes', [$expectedMinutesPerDay])
                    ->groupBy('employee_profile_id');
            },
            'receivedLeave' => function ($query) use ($year_of, $month_of, $expectedMinutesPerDay) {
                $query->whereYear('date_from', $year_of)
                    ->whereMonth('date_from', $month_of)
                    ->where('status', 'received')
                    ->selectRaw('employee_profile_id, SUM(DATEDIFF(date_to, date_from) + 1) * ? as total_leave_minutes', [$expectedMinutesPerDay])
                    ->groupBy('employee_profile_id');
            },
            'dtrInvalidEntry' => function ($query) use ($year_of, $month_of) {
                $query->whereYear('dtr_date', $year_of)
                    ->whereMonth('dtr_date', $month_of);
            },
            'schedule' => function ($query) use ($year_of, $month_of) {
                $query->whereYear('date', $year_of)
                    ->whereMonth('date', $month_of);
            },
            'employeeDtr' => function ($query) use ($year_of, $month_of) {
                $query->whereYear('dtr_date', $year_of)
                    ->whereMonth('dtr_date', $month_of);
            },
            'leaveApplications' => function ($query) use ($year_of, $month_of) {
                $query->where(function ($query) use ($year_of, $month_of) {
                    // Include records where the leave period starts or ends within the month
                    $query->whereYear('date_from', $year_of)
                        ->whereMonth('date_from', $month_of)
                        ->orWhere(function ($query) use ($year_of, $month_of) {
                        $query->whereYear('date_to', $year_of)
                            ->whereMonth('date_to', $month_of);
                    });
                })->where('status', 'received');
            },
            'nigthDuties' => function ($query) use ($year_of, $month_of) {
                $query->whereYear('dtr_date', $year_of)
                    ->whereMonth('dtr_date', $month_of);
            }
        ])
            ->where('biometric_id', 139)
            ->get();

        $holiday = DB::connection('mysql2')->table('holidays')->whereRaw("LEFT(month_day, 2) = ?", [str_pad($month_of, 2, '0', STR_PAD_LEFT)])->get();


        $data = $employee_data->map(function ($employee) use ($year_of, $month_of, $totalDaysInMonth, $request, $expectedMinutesPerDay, $holiday, $helper) {
            $biometric_id = $employee->biometric_id;

            $NoOfInvalidEntry = [];
            $nightDifferentials = [];

            // Skip if employee area is not assigned
            if (!$employee->assignedArea)
                return null;

            // Handle payroll periodsJ
            $payrollPeriodStart = 1;
            $payrollPeriodEnd = $totalDaysInMonth;

            // Pre-fetch counts and related values
            $scheduleCount = $employee->schedule->count();
            $NoOfInvalidEntry = $employee->dtrInvalidEntry->count();
            $receivedLeave = $employee->receivedLeave;

            // Salary details
            $salary_grade = $employee->assignedArea->salary_grade_id;
            $salary_step = $employee->assignedArea->salary_grade_step;

            // Extract totals safely with null coalescing (CALCULATED VALUES GROUP)
            $totalWorkingMinutes = $employee->dailyTimeRecords->first()->total_working_minutes ?? 0;
            $totalOBMinutes = $employee->approvedOB->first()->total_ob_minutes ?? 0;
            $totalOTMinutes = $employee->approvedOT->first()->total_ot_minutes ?? 0;
            $totalLeaveMinutes = $employee->receivedLeave->first()->total_leave_minutes ?? 0;
            $totalOvertimeMinutes = $employee->dailyTimeRecords->first()->total_overtime_minutes ?? 0;
            $totalUnderTimeMinutes = $employee->dailyTimeRecords->first()->total_undertime_minutes ?? 0;

            $totalMinutes = $totalWorkingMinutes + $totalOBMinutes + $totalOTMinutes + $totalLeaveMinutes;
            $totalWorkingHours = $totalMinutes / 60;
            $noOfPresentDays = round($totalMinutes / $expectedMinutesPerDay, 1);

            // Leave calculations
            $noOfLeaveWoPay = $receivedLeave->where('without_pay', true)->count();
            $noOfLeaveWPay = $receivedLeave->where('without_pay', false)->count();

            // Absence and day-off calculations
            $noOfAbsences = $scheduleCount - $noOfPresentDays;
            $noOfDayOff = $totalDaysInMonth - $scheduleCount;

            if ($employee->employmentType->name === "Job Order") {
                if ($request->first_half) {
                    $payrollPeriodStart = 1;
                    $payrollPeriodEnd = 15;
                } elseif ($request->second_half) {
                    $payrollPeriodStart = 16;
                    $payrollPeriodEnd = $totalDaysInMonth;
                }
            }

            // Holiday Pay(Regular Employee)
            $holidayPay = 0;
            if ($employee->employmentType->name !== "Job Order") {
                $dtrDates = $employee->employeeDtr
                    ->map(function ($dtr) {
                        return Carbon::parse($dtr->dtr_date)->format('m-d');
                    })
                    ->toArray();

                $scheduleDates = $employee->schedule
                    ->map(function ($schedule) {
                        return Carbon::parse($schedule->date)->format('m-d');
                    })
                    ->toArray();

                // Loop through holidays in the current month
                foreach ($holiday as $holidayRecord) {
                    $holidayMonthDay = $holidayRecord->month_day;

                    // Check if the employee has a schedule on the holiday
                    $isScheduledOnHoliday = in_array($holidayMonthDay, $scheduleDates);

                    // Check if there is no DTR entry on the holiday
                    $hasNoDtrOnHoliday = !in_array($holidayMonthDay, $dtrDates);

                    // If scheduled on a holiday but has no DTR, add holiday pay
                    if ($isScheduledOnHoliday && $hasNoDtrOnHoliday) {
                        $holidayPay++;
                    }
                }
            }

            // Salary computations (SALARY VALUES GROUP)
            $computed = new ComputationController();
            $basicSalary = $computed->BasicSalary($salary_grade, $salary_step, $scheduleCount)['GrandTotal'];
            $rates = $computed->Rates($basicSalary, $scheduleCount);
            $grossSalary = $computed->GrossSalary($noOfPresentDays, $basicSalary, $noOfAbsences);

            $absentRate = $computed->AbsentRates($noOfAbsences, $rates);
            $undertimeRate = $computed->UndertimeRates($totalUnderTimeMinutes, $rates);

            $netSalary = $computed->NetSalaryFromTimeDeduction($grossSalary, $undertimeRate, $absentRate, $employee->employmentType->name);

            // Calculate Holiday Pay (in monetary terms)
            $ratePerDay = $rates['Daily'];
            $holidayPayRate = $ratePerDay * $holidayPay;

            // Overall Net Salary (Net Salary + Holiday Pay)
            $overallNetSalary = $netSalary + $holidayPayRate;

            // //This function Return True or False
            $OutOfPayroll = $computed->OutofPayroll($netSalary, $employee->employmentType, $totalMinutes);

            $first_in = $employee->nigthDuties->first()->first_in ?? null;
            $first_out = $employee->nigthDuties->first()->first_out ?? null;
            $nightDifferentials[] = $this->getNightDifferentialHours($first_in, $first_out, $biometric_id, [], $employee->schedule);

            $nightDiff = array_values(array_filter($nightDifferentials, function ($row) use ($biometric_id) {
                return $row['biometric_id'] ?? null === $biometric_id;
            }));

            return [
                'Biometric_id' => $biometric_id,
                'employee_id' => $employee->employee_id,
                'Payroll' => "{$payrollPeriodStart} - {$payrollPeriodEnd}",
                'From' => $payrollPeriodStart,
                'To' => $payrollPeriodEnd,
                'Month' => $month_of,
                'Year' => $year_of,
                'Is_out' => $OutOfPayroll,
                "NightDifferentials" => $nightDiff,

                // Calculated values
                'TotalWorkingMinutes' => $totalMinutes,
                'TotalWorkingHours' => $totalWorkingHours,
                'TotalOvertimeMinutes' => $totalOvertimeMinutes,
                'TotalUndertimeMinutes' => $totalUnderTimeMinutes,
                'NoofPresentDays' => $noOfPresentDays,
                'NoofLeaveWoPay' => $noOfLeaveWoPay,
                'NoofLeaveWPay' => $noOfLeaveWPay,
                'NoofAbsences' => $noOfAbsences,
                "NoofInvalidEntry" => $NoOfInvalidEntry,
                'NoofDayOff' => $noOfDayOff,
                'schedule' => $scheduleCount,

                // Salary values
                'GrandBasicSalary' => $basicSalary,
                'Rates' => $rates,
                'GrossSalary' => $grossSalary,
                'TimeDeductions' => [
                    'AbsentRate' => $absentRate,
                    'UndertimeRate' => $undertimeRate,
                ],
                'NetSalary' => $netSalary,
                'OverallNetSalary' => $overallNetSalary,
                'Employee' => [
                    'profile_id' => $employee->id,
                    'employee_id' => $employee->employee_id,
                    'Information' => $employee->personalInformation,
                    // 'Designation' => $employee->findDesignation(),
                    'Hired' => $employee->date_hired,
                    'EmploymentType' => $employee->employmentType,
                    'Excluded' => InActiveEmployee::where('employee_id', $employee->employee_id)->first(),
                    'leaveApplications' => $employee->leaveApplications->isNotEmpty() ? [
                        'country' => $employee->leaveApplications->first()->country ?? null,
                        'city' => $employee->leaveApplications->first()->city ?? null,
                        'from' => $employee->leaveApplications->first()->date_from ?? null,
                        'to' => $employee->leaveApplications->first()->date_to ?? null,
                        'leavetype' => LeaveType::find($employee->leaveApplications->first()->leave_type_id ?? null)->name ?? "",
                        'without_pay' => $employee->leaveApplications->first()->without_pay ?? null,
                        'dates_covered' => $helper->getDateIntervals($employee->leaveApplications->first()->date_from ?? null, $employee->leaveApplications->first()->date_to ?? null),
                    ] : [],
                    'employeeLeaveCredits' => $employee->employeeLeaveCredits

                ],
                'Assigned_area' => $employee->assignedArea->findDetails(),
                'SalaryData' => [
                    'step' => $employee->assignedArea->salary_grade_step,
                    'salaryGroup' => $employee->assignedArea->salaryGrade,
                ],
            ];

        });

        return response()->json($data->filter());
    }

    public function getNightDifferentialHours($startTime, $endTime, $biometric_id, $wBreak, $DaySchedule)
    {
        if (count($wBreak) == 0 && $startTime && $endTime && count($DaySchedule)) {

            // Convert start and end times to DateTime objects
            $startTime = new \DateTime($startTime);
            $endTime = new \DateTime($endTime);


            // Ensure that the end time is after the start time
            if ($endTime <= $startTime) {
                $endTime->modify('+1 day');
            }

            $totalMinutes = 0;
            $totalHours = 0;
            $details = [];

            // Loop through each day in the range
            $current = clone $startTime;

            while ($current <= $endTime) {
                // Calculate night period overlaps for the current day
                $nightStart = (clone $current)->setTime(18, 0, 0);
                $midnight = (clone $current)->setTime(0, 0, 0)->modify('+1 day');
                $nightEnd = (clone $current)->setTime(6, 0, 0)->modify('+1 day');

                // Calculate overlap with night period for the first day (6 PM to 12 AM)
                $overlapStart = max($current, $nightStart);
                $overlapEnd = min($endTime, $midnight);

                // Calculate total minutes and hours for the first day overlap (6 PM to 12 AM)
                if ($overlapStart <= $overlapEnd) {
                    $interval = $overlapStart->diff($overlapEnd);
                    $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
                    $hours = $interval->h + ($interval->i / 60);

                    $details[] = [
                        'minutes' => $minutes,
                        'hours' => round($hours, 0),
                        'date' => $overlapStart->format('Y-m-d'),
                        'period' => '6 PM to 12 AM',
                        'biometric_id' => $biometric_id,
                    ];

                    $totalMinutes += $minutes;
                    $totalHours += $hours;
                }

                // Check if there is an overlap into the next day (12 AM to 6 AM)
                if ($endTime > $midnight) {
                    $nextDayOverlapStart = $midnight;
                    $nextDayOverlapEnd = min($endTime, $nightEnd);

                    $interval = $nextDayOverlapStart->diff($nextDayOverlapEnd);
                    $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
                    $hours = $interval->h + ($interval->i / 60);


                    $details[] = [
                        'minutes' => $minutes,
                        'hours' => round($hours, 0),
                        'date' => $nextDayOverlapStart->format('Y-m-d'),
                        'period' => '12 AM to 6 AM',
                        'biometric_id' => $biometric_id,
                    ];

                    $totalMinutes += $minutes;
                    $totalHours += $hours;
                }

                // Move to the next day
                $current->modify('+1 day')->setTime(0, 0, 0);
            }


            if ($totalMinutes && $totalHours) {
                return [
                    'biometric_id' => $biometric_id,
                    'total_minutes' => $totalMinutes,
                    'total_hours' => round($totalHours, 0),
                    'details' => $details,
                ];
            }
        }

        return null; // Return null if the conditions are not met
    }

}
