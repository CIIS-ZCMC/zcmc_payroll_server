<?php

namespace App\Http\Controllers\UMIS;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeTimeRecordResource;
use App\Http\Resources\PayrollPeriodResource;
use App\Models\Employee;
use App\Models\EmployeeComputedSalary;
use App\Models\EmployeeSalary;
use App\Models\EmployeeTimeRecord;
use App\Models\ExcludedEmployee;
use App\Models\PayrollPeriod;
use App\Models\UMIS\EmployeeProfile;
use App\Models\UMIS\InActiveEmployee;
use App\Models\UMIS\LeaveType;
use App\Services\ComputationService;
use App\Services\EmployeeSalaryService;
use App\Services\EmployeeService;
use App\Services\EmployeeTimeRecordService;
use App\Services\ExcludeEmployeeService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Str;


class EmployeeProfileController extends Controller
{

    protected $Working_Days;
    protected $Working_Hours;

    protected $employeeService;
    protected $excludedEmployeeService;
    protected $employeeSalaryService;
    protected $employeeTimeRecordService;
    protected $computationService;

    public function __construct(
        EmployeeService $employeeService,
        ExcludeEmployeeService $excludedEmployeeService,
        EmployeeSalaryService $employeeSalaryService,
        EmployeeTimeRecordService $employeeTimeRecordService,
        ComputationService $computationService
    ) {
        $this->Working_Days = 22;
        $this->Working_Hours = 8;

        $this->employeeService = $employeeService;
        $this->excludedEmployeeService = $excludedEmployeeService;
        $this->employeeSalaryService = $employeeSalaryService;
        $this->employeeTimeRecordService = $employeeTimeRecordService;
        $this->computationService = $computationService;
    }

    // Step 1 (Complete)
    public function fetchStep1(Request $request)
    {
        try {
            $year_of = $request->year_of;
            $month_of = $request->month_of;

            $period_type = $request->period_type;
            $first_half = $request->first_half ?? null;
            $second_half = $request->second_half ?? null;

            $employment_type = $request->employment_type;

            $currentyear = date('Y');
            $currentMonth = date('m');

            //Payroll Headers Validation
            if (!$first_half && !$second_half) {
                if ($currentyear == $year_of && $currentMonth == $month_of) {
                    return response()->json(['error' => 'Generation failed', 'message' => "Could not generate latest records", 'statusCode' => 500]);
                }

                if ($currentyear == $year_of && $currentMonth < $month_of) {
                    return response()->json(['error' => 'Generation failed', 'message' => "Could not generate future months", 'statusCode' => 500]);
                }

                if ($currentMonth > $month_of) {
                    if (($currentMonth - $month_of) == 1) {
                        if (floor(date('d', strtotime($year_of . "-" . $month_of . "-" . date('d')))) <= 11) {
                            return response()->json(['error' => 'Generation failed', 'message' => "Could not generate latest records", 'statusCode' => 500]);
                        }
                    }
                }
            }

            //Employee Fetching Function Start Here
            $totalDaysInMonth = Carbon::createFromDate($year_of, $month_of, 1)->daysInMonth;
            $expectedMinutesPerDay = 480;

            // Handle payroll periods
            $payrollPeriodStart = 1;
            $payrollPeriodEnd = $totalDaysInMonth;
            $helper = new Helpers();

            $employee_data = EmployeeProfile::with([
                'employmentType',
                'personalInformation',
                'assignedArea' => function ($query) {
                    $query->with(['salaryGrade']);
                },
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
            ]);

            if ($employment_type === 'Job Order') {
                $employee_data->whereHas('employmentType', function ($query) {
                    $query->where('name', 'Job Order');
                });
            } else {
                $employee_data->whereHas('employmentType', function ($query) {
                    $query->where('name', '!=', 'Job Order');
                });
            }

            $employee_data = $employee_data->get();

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
                $totalWorkingMinutes = round($employee->dailyTimeRecords->first()->total_working_minutes ?? 0);
                $totalOBMinutes = round($employee->approvedOB->first()->total_ob_minutes ?? 0);
                $totalOTMinutes = round($employee->approvedOT->first()->total_ot_minutes ?? 0);
                $totalLeaveMinutes = round((int) optional($employee->receivedLeave->first())->total_leave_minutes ?? 0);
                $totalOvertimeMinutes = round((int) optional($employee->dailyTimeRecords->first())->total_overtime_minutes ?? 0);
                $totalUnderTimeMinutes = round((int) optional($employee->dailyTimeRecords->first())->total_undertime_minutes ?? 0);

                //  Total working details
                $totalMinutes = round((int) $totalWorkingMinutes, 2);
                $totalWorkingHours = round($totalMinutes / 60, 2);
                $noOfPresentDays = round($totalMinutes / $expectedMinutesPerDay, 1);

                $totalWorkingMinutesWithLeave = round($totalMinutes + $totalLeaveMinutes + $totalOBMinutes + $totalOTMinutes, 2);
                $totalWorkingHoursWithLeave = round($totalWorkingMinutesWithLeave / 60, 2);
                $noOfPresentDaysWithLeave = round($totalWorkingMinutesWithLeave / $expectedMinutesPerDay, 1);

                // Leave calculations
                $leaveWoPayMinutes = (int) optional($receivedLeave->where('without_pay', true)->first())->total_leave_minutes;
                $leaveWPayMinutes = (int) optional($receivedLeave->where('without_pay', false)->first())->total_leave_minutes;
                $noOfLeaveWoPay = round($leaveWoPayMinutes / $expectedMinutesPerDay, 1);
                $noOfLeaveWPay = round($leaveWPayMinutes / $expectedMinutesPerDay, 1);

                // Absence and day-off calculations
                $absences = $employee->getAbsentDates($year_of, $month_of);
                $noOfAbsences = $absences['count'];
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

                //  Map Salary Grade
                $salaryGrade = $employee->assignedArea->salaryGrade;
                $mapTranch = [
                    'first' => 'one',
                    'second' => 'two',
                    'third' => 'three',
                    'fourth' => 'four',
                    'fifth' => 'five',
                    'sixth' => 'six',
                    'seventh' => 'seven',
                    'eighth' => 'eight'

                ];

                $stepKey = $mapTranch[$salaryGrade['tranch']] ?? null;
                $salary_details = [
                    'salary_grade_number' => $salaryGrade['salary_grade_number'],
                    'tranch' => $salaryGrade['tranch'],
                    'amount' => $stepKey ? $salaryGrade[$stepKey] : null,
                ];

                //  Salary rate computations (SALARY VALUES GROUP)
                $basicSalary = $salary_details['amount'];
                $rates = $this->salaryRate($basicSalary);
                $initialNetPay = $this->netPay($basicSalary, $noOfPresentDaysWithLeave); //Initial pay, calculate only number present days
                $absentRate = $this->absentRate($noOfAbsences, $rates['daily']);
                $undertimeRate = $this->undertimeRate($totalUnderTimeMinutes, $rates['minutes']);

                //  Calculate Holiday Pay (in monetary terms)
                $ratePerDay = $rates['daily'];
                $holidayPayRate = round($ratePerDay * $holidayPay, 2);

                //  Overall Net Pay (Initial Net Pay + Holiday Pay)
                $netPay = round($initialNetPay + $holidayPayRate, 2);

                //  This function Return True or False
                $salaryLimit = $employee->employmentType->name === "Job Order" ? 2500 : 5000;
                $outOfPayroll = $netPay <= $salaryLimit ? 'true' : 'false'; //if net_pay or less than salary limit, then out of payroll

                $night_duties = $employee->nigthDuties;
                

                //Applied filtering in night duties
                $nightDiff = $night_duties
                ->filter(function ($duty) use ($month_of, $year_of) {
                    return (int)date("m", strtotime($duty->dtr_date)) === (int)$month_of
                        && (int)date("Y", strtotime($duty->dtr_date)) === (int)$year_of;
                })
                ->map(function ($duty) {
                    return [
                        'biometric_id' => $duty->biometric_id,
                        'dtr_date' => $duty->dtr_date,
                        'first_in' => $duty->first_in,
                        'first_out' => $duty->first_out,
                        'second_in' => $duty->second_in,
                        'second_out' => $duty->second_out,
                        'total_working_minutes' => $duty->total_working_minutes,
                        'overtime_minutes' => $duty->overtime_minutes ?? 0,
                        'undertime_minutes' => $duty->undertime_minutes ?? 0,
                        'overall_minutes_rendered' => $duty->overall_minutes_rendered,
                    ];
                })
                ->values()
                ->toArray();
            

                // Get the overall total working minutes of night duties
                $overallNightDutyMinutes = $night_duties->sum('total_working_minutes');
                $total_night_duty_hours = round($overallNightDutyMinutes / 60, 2);

                //  Get Absent dates
                $absent_date = $absences['dates'];

                $is_inactive = InActiveEmployee::where('employee_id', $employee->employee_id)->exists();

                return [
                    'employee_profile_id' => $employee->id,
                    'employee_number' => $employee->employee_id,
                    'biometric_id' => $biometric_id,
                    'personal_information' => $employee->personalInformation,
                    'designation' => $employee->findDesignation(),
                    'employment_type' => $employee->employmentType,
                    'assigned_area' => $employee->assignedArea->findDetails(),
                    'hired' => $employee->date_hired,
                    'salary_step' => $salary_step,
                    'salary_grade' => $salary_grade,
                    'is_inactive' => $is_inactive,
                    'employee_leave_credits' => $employee->employeeLeaveCredits,
                    'leave_applications' => $employee->leaveApplications->isNotEmpty() ? [
                        'country' => $employee->leaveApplications->first()->country ?? null,
                        'city' => $employee->leaveApplications->first()->city ?? null,
                        'from' => $employee->leaveApplications->first()->date_from ?? null,
                        'to' => $employee->leaveApplications->first()->date_to ?? null,
                        'leave_type' => LeaveType::find($employee->leaveApplications->first()->leave_type_id ?? null)->name ?? null,
                        'without_pay' => $employee->leaveApplications->first()->without_pay ?? null,
                        'dates_covered' => $this->getDateIntervals($employee->leaveApplications->first()->date_from ?? null, $employee->leaveApplications->first()->date_to ?? null),
                    ] : [],

                    'payroll' => [
                        'payroll_period' => "{$payrollPeriodStart} - {$payrollPeriodEnd}",
                        'from' => $payrollPeriodStart,
                        'to' => $payrollPeriodEnd,
                        'month' => $month_of,
                        'year' => $year_of,
                    ],

                    'time_record' => [
                        'is_out' => $outOfPayroll,
                        'rates' => $rates,
                        'absent_rate' => $absentRate,
                        'undertime_rate' => $undertimeRate,
                        'base_salary' => $basicSalary,
                        'initial_net_pay' => $initialNetPay,
                        'net_pay' => $netPay,
                        "night_differentials" => $nightDiff,
                        'total_working_minutes' => $totalMinutes,
                        'total_working_minutes_with_leave' => $totalWorkingMinutesWithLeave,
                        'total_working_hours' => $totalWorkingHours,
                        'total_working_hours_with_leave' => $totalWorkingHoursWithLeave,
                        'total_overtime_minutes' => $totalOvertimeMinutes,
                        'total_undertime_minutes' => $totalUnderTimeMinutes,
                        'total_official_business_minutes' => $totalOBMinutes,
                        'total_official_time_minutes' => $totalOTMinutes,
                        'total_leave_minutes' => $totalLeaveMinutes,
                        'total_night_duty_hours' => $total_night_duty_hours,
                        'no_of_present_days' => $noOfPresentDays,
                        'no_of_present_days_with_leave' => $noOfPresentDaysWithLeave,
                        'no_of_leave_wo_pay' => $noOfLeaveWoPay,
                        'no_of_leave_w_pay' => $noOfLeaveWPay,
                        'no_of_absences' => $noOfAbsences,
                        'no_of_invalid_entry' => $NoOfInvalidEntry,
                        'no_of_day_off' => $noOfDayOff,
                        'no_of_schedule' => $scheduleCount,
                        'absent_dates' => $absent_date,
                    ],
                ];
            });

                $response_data = [
                "employment_type" => $employment_type,
                "month_of" => $month_of,
                "year_of" => $year_of,
                "period_type" => $period_type,
                "period_start" => $payrollPeriodStart,
                "period_end" => $payrollPeriodEnd,
                "first_half" => $first_half,
                "second_half" => $second_half,
                "employees" => $data
            ];
          

            $uuid = Str::uuid();

            Cache::put($uuid, json_encode($response_data));

            return response()->json([
                'data' => ['uuid' => $uuid],
                'message' => "Successfully Fetched. Step 1",
                'statusCode' => 200
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {

            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Step 2 (Complete)
    public function fetchStep2(Request $request)
    {
        $cache_data = json_decode(Cache::get($request->uuid), true);

        $employees = $cache_data['employees'];
        $employment_type = $cache_data['employment_type'];
        $month_of = $cache_data['month_of'];
        $year_of = $cache_data['year_of'];
        $period_type = $cache_data['period_type'];
        $period_start = $cache_data['period_start'];
        $period_end = $cache_data['period_end'];
        $first_half = $cache_data['first_half'];
        $second_half = $cache_data['second_half'];

        if ($period_type === 'second_half') {
            $firstHalf = PayrollPeriod::where('period_type', 'first_half')
                ->where('employment_type', $employment_type)
                ->where('month', $month_of)
                ->where('year', $year_of)
                ->first();

            if ($firstHalf && !$firstHalf->locked_at) {
                return response()->json([
                    'message' => "First half payroll period must be locked before creating second half",
                    'statusCode' => 403
                ], 403);
            }
        }

        // Find or create payroll period
        $payroll_period = PayrollPeriod::firstOrNew([
            'payroll_type' => "General Payroll",
            'period_type' => $period_type,
            'employment_type' => $employment_type,
            'month' => $month_of,
            'year' => $year_of
        ]);

        // Prepare update data
        $payrollData = [
            'period_start' => $period_start,
            'period_end' => $period_end,
            'days_of_duty' => $this->Working_Days,
            'is_active' => true
        ];

        // Create/update payroll period
        if ($payroll_period->exists) {
            if ($payroll_period->locked_at) {
                return response()->json([
                    'message' => "Payroll is already locked",
                    'statusCode' => 403
                ], 403);
            }
            $payroll_period->update($payrollData);
        } else {
            $payroll_period->fill($payrollData)->save();
        }

        // Deactivate other periods
        PayrollPeriod::where('id', '!=', $payroll_period->id)->update(['is_active' => false]);

        $period_id = $payroll_period->id;
        $period_type = $payroll_period->period_type;

        $employee_data = [];
        foreach ($employees as $data) {
            if (is_array($data)) {
                $find_employee = Employee::where('employee_number', $data['employee_number'])->first();

                $employee_details = [
                    'employee_profile_id' => $data['employee_profile_id'],
                    'employee_number' => $data['employee_number'],
                    'first_name' => $data['personal_information']['first_name'],
                    'middle_name' => $data['personal_information']['middle_name'],
                    'last_name' => $data['personal_information']['last_name'],
                    'extension_name' => $data['personal_information']['name_extension'],
                    'designation' => $data['designation']['name'],
                    'assigned_area' => json_encode($data['assigned_area']),
                    'is_newly_hired' => false,
                    'is_excluded' => $data['time_record']['is_out'] === 'true' ? 1 : 0,
                    'is_resigned' => false,
                    'status' => $data['is_inactive'] === false ? true : false,
                ];

                $employee = $find_employee !== null
                    ? $this->employeeService->update($find_employee->id, $employee_details)
                    : $this->employeeService->create($employee_details);

                $excluded_employee_details = [
                    'employee_id' => $employee->id,
                    'payroll_period_id' => $period_id,
                    'month' => $month_of,
                    'year' => $year_of,
                    'period_start' => $payroll_period->period_start,
                    'period_end' => $payroll_period->period_end,
                    'reason' => $this->getExclusionReason($data),
                    'is_removed' => false,
                ];

                $employee_salary_details = [
                    'employee_id' => $employee->id,
                    'payroll_period_id' => $period_id,
                    'employment_type' => $data['employment_type']['name'],
                    'base_salary' => encrypt($data['time_record']['base_salary']),
                    'salary_grade' => $data['salary_grade'],
                    'salary_step' => $data['salary_step'],
                    'month' => $data['payroll']['month'],
                    'year' => $data['payroll']['year'],
                    'is_active' => true,
                ];

                // Handle ExcludedEmployee if out of payroll
                if ($data['time_record']['is_out'] === 'true') {
                    $find_excluded_employee = ExcludedEmployee::where('employee_id', $employee->id)
                        ->where('month', $month_of)
                        ->where('year', $year_of)
                        ->first();

                    $find_excluded_employee === null
                        ? $this->excludedEmployeeService->create($excluded_employee_details)
                        : $this->excludedEmployeeService->update($find_excluded_employee->id, $excluded_employee_details);
                }

                // Handle EmployeeSalary
                $find_employee_salary = EmployeeSalary::where('employee_id', $employee->id)
                    ->where('month', $month_of)
                    ->where('year', $year_of)
                    ->first();

                if ($find_employee_salary === null) {
                    $this->employeeSalaryService->create($employee_salary_details);
                } elseif ($find_employee_salary->employment_type !== $data['employment_type']['name']) {
                    $find_employee_salary->update(['is_active', false]);
                    $this->employeeSalaryService->create($employee_salary_details);
                } else {
                    $this->employeeSalaryService->update($find_employee_salary->id, $employee_salary_details);
                }

                $employee_data[] = array_merge($employee->toArray(), $data);
            }
        }

        // Update other employee salaries to is_active = 0
        EmployeeSalary::where('month', '!=', $month_of)
            ->where('year', '!=', $year_of)
            ->update(['is_active' => false]);

        $response_data = [
            "employment_type" => $cache_data['employment_type'],
            'month_of' => $cache_data['month_of'],
            'year_of' => $cache_data['year_of'],
            'period_id' => $period_id,
            "period_type" => $period_type,
            "period_start" => $cache_data['period_start'],
            "period_end" => $cache_data['period_end'],
            'first_half' => $cache_data['first_half'],
            'second_half' => $cache_data['second_half'],
            'employees' => $employee_data
        ];

        $uuid = Str::uuid();

        Cache::put($uuid, json_encode($response_data));

        return response()->json([
            'data' => ['uuid' => $uuid],
            'message' => "Data Successfully saved (Step 2)",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    // Step 3 (Complete)
    public function fetchStep3(Request $request)
    {
        $cache_data = json_decode(Cache::get($request->uuid), true);

        $time_record = [];
        $employees = $cache_data['employees'];
        $month_of = $cache_data['month_of'];
        $year_of = $cache_data['year_of'];
        $period_id = $cache_data['period_id'];
        $period_type = $cache_data['period_type'];
        $period_start = $cache_data['period_start'];
        $period_end = $cache_data['period_end'];
        $first_half = $cache_data['first_half'];
        $second_half = $cache_data['second_half'];
        $payroll_employment_type = $cache_data['employment_type'];

        $defaultMonthCount = cal_days_in_month(CAL_GREGORIAN, $month_of, $year_of);
        $from = 1;
        $to = $defaultMonthCount;

        $result = null;
        foreach ($employees as $data) {
            // $employment_type = $data['employment_type']['name'];

            // // Adjust period for Job Order employees
            // if ($employment_type === "Job Order") {
            //     if ($first_half) {
            //         $from = 1;
            //         $to = 15;
            //     } elseif ($second_half) {
            //         $from = 16;
            //         $to = $defaultMonthCount;
            //     }
            // }

            $employment_type = strtolower($data['employment_type']['name']);

            // Filter employees based on payroll employment type
            $valid_employment_types = [];

            if ($payroll_employment_type === 'permanent') {
                $valid_employment_types = [
                    'permanent full-time',
                    'permanent part-time',
                    'permanent cti',
                    'temporary'
                ];
            } elseif ($payroll_employment_type === 'job order') {
                $valid_employment_types = ['job order'];
            }

            if (!in_array($employment_type, $valid_employment_types)) {
                continue; // Skip this employee
            }

            // Adjust period for Job Order employees
            if ($employment_type === "job order") {
                if ($first_half) {
                    $from = 1;
                    $to = 15;
                } elseif ($second_half) {
                    $from = 16;
                    $to = $defaultMonthCount;
                }
            }

            $status = $data['time_record']['is_out'] === 'true' ? 'excluded' : 'included';

            $employee_time_record_details = [
                'employee_id' => $data['id'],
                'payroll_period_id' => $period_id,
                'minutes' => $data['time_record']['rates']['minutes'],
                'daily' => $data['time_record']['rates']['daily'],
                'hourly' => $data['time_record']['rates']['hourly'],
                'absent_rate' => $data['time_record']['absent_rate'],
                'undertime_rate' => $data['time_record']['undertime_rate'],
                'base_salary' => $data['time_record']['base_salary'],
                'initial_net_pay' => $data['time_record']['initial_net_pay'],
                'net_pay' => $data['time_record']['net_pay'],
                'total_working_minutes' => $data['time_record']['total_working_minutes'],
                'total_working_minutes_with_leave' => $data['time_record']['total_working_minutes_with_leave'],
                'total_working_hours' => $data['time_record']['total_working_hours'],
                'total_working_hours_with_leave' => $data['time_record']['total_working_hours_with_leave'],
                'total_overtime_minutes' => $data['time_record']['total_overtime_minutes'],
                'total_undertime_minutes' => $data['time_record']['total_undertime_minutes'],
                'total_official_business_minutes' => $data['time_record']['total_official_business_minutes'],
                'total_official_time_minutes' => $data['time_record']['total_official_time_minutes'],
                'total_leave_minutes' => $data['time_record']['total_leave_minutes'],
                'total_night_duty_hours' => $data['time_record']['total_night_duty_hours'],
                'no_of_present_days' => $data['time_record']['no_of_present_days'],
                'no_of_present_days_with_leave' => $data['time_record']['no_of_present_days_with_leave'],
                'no_of_leave_wo_pay' => $data['time_record']['no_of_leave_wo_pay'],
                'no_of_leave_w_pay' => $data['time_record']['no_of_leave_w_pay'],
                'no_of_absences' => $data['time_record']['no_of_absences'],
                'no_of_invalid_entry' => $data['time_record']['no_of_invalid_entry'],
                'no_of_day_off' => $data['time_record']['no_of_day_off'],
                'no_of_schedule' => $data['time_record']['no_of_schedule'],
                'night_differentials' => json_encode($data['time_record']['night_differentials']),
                'absent_dates' => json_encode($data['time_record']['absent_dates']),
                'month' => $data['payroll']['month'],
                'year' => $data['payroll']['year'],
                'from' => $data['payroll']['from'],
                'to' => $data['payroll']['to'],
                'is_night_shift' => false,
                'is_active' => true,
                'status' => $status,
            ];

            // Check if time record exists
            $find_employee_time_record = EmployeeTimeRecord::where('employee_id', $data['id'])
                ->where('payroll_period_id', $period_id)
                ->where('month', $month_of)
                ->where('year', $year_of);

            if ($employment_type === "Job Order") {
                $find_employee_time_record->where('from', $from)
                    ->where('to', $to);
            }

            $find_result = $find_employee_time_record->first();

            !$find_result
                ? $result = $this->employeeTimeRecordService->create($employee_time_record_details)
                : $result = $this->employeeTimeRecordService->update($find_result->id, $employee_time_record_details);

            EmployeeComputedSalary::updateOrCreate(
                [
                    'employee_id' => $result->employee_id,
                    'employee_time_record_id' => $result->id
                ],
                [
                    'computed_salary' => $result->net_pay
                ]
            );

            // Deactivate other time records
            EmployeeTimeRecord::where('payroll_period_id', '!=', $period_id)
                ->where('month', '!=', $month_of)
                ->where('year', '!=', $year_of)
                ->update(['is_active' => false]);

            $time_record[] = $result;

        }

        $response_data = [
            "employment_type" => $cache_data['employment_type'],
            'month_of' => $cache_data['month_of'],
            'year_of' => $cache_data['year_of'],
            "period_id" => $cache_data['period_id'],
            "period_type" => $cache_data['period_type'],
            "period_start" => $cache_data['period_start'],
            "period_end" => $cache_data['period_end'],
            'first_half' => $cache_data['first_half'],
            'second_half' => $cache_data['second_half'],
            'employees' => EmployeeTimeRecordResource::collection($time_record)
        ];

        $uuid = Str::uuid();

        Cache::put($uuid, json_encode($response_data));

        return response()->json([
            'data' => ['uuid' => $uuid],
            'message' => "Data Successfully saved (Step 3)",
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    // Step 4 (Complete)
    public function fetchStep4(Request $request)
    {
        $cache_data = json_decode(Cache::get($request->uuid), true);
        $employees = $cache_data['employees'];

        foreach ($employees as $data) {
            $payroll_period_id = $data['payroll_period']['id'];
            $employee_id = $data['employee']['id'];
            $employment_type = $data['employee']['salary']['employment_type'];
            $salary_grade = $data['employee']['salary']['salary_grade'];
            $basic_salary = $data['employee']['salary']['base_salary'];
            $no_of_present_days = ['no_of_present_days']; //without leave, ob and ot , also holiday not included
            $no_of_present_days_with_leave = ['no_of_present_days_with_leave'];
            $no_of_absences = $data['no_of_absences'];
            $no_of_leave_wo_pay = $data['no_of_leave_wo_pay'];
            $no_of_leave_w_pay = $data['no_of_leave_w_pay'];
            $no_of_leave_days = $no_of_leave_wo_pay + $no_of_leave_w_pay;

            $is_part_time = $employment_type === 'Permanent Part-time' ? true : false;

            if ($employment_type !== "Job Order") {
                $hazard = $this->computationService->hazard(
                    $payroll_period_id,
                    $employee_id,
                    $employment_type,
                    $salary_grade,
                    $basic_salary,
                    $is_part_time,
                    $no_of_absences,
                    $no_of_leave_days
                );

                $pera = $this->computationService->pera(
                    $payroll_period_id,
                    $employee_id,
                    $no_of_present_days_with_leave,
                    $employment_type,
                    22,
                    $no_of_absences
                );
                // $hazard = $this->computationService->hazardPay($payroll_period_id, $employee_id, $employment_type, $salary_grade, $basic_salary, $no_of_present_days);
                $deduction = $this->computationService->employeeDeduction(
                    $payroll_period_id,
                    $employee_id
                );
            }
        }

        Cache::forget($request->uuid);

        $response_data = [
            "employment_type" => $cache_data['employment_type'],
            'month_of' => $cache_data['month_of'],
            'year_of' => $cache_data['year_of'],
            "period_id" => $cache_data['period_id'],
            "period_type" => $cache_data['period_type'],
            "period_start" => $cache_data['period_start'],
            "period_end" => $cache_data['period_end'],
            'first_half' => $cache_data['first_half'],
            'second_half' => $cache_data['second_half'],
        ];

        $payroll_period = PayrollPeriod::whereNull('locked_at')
            ->where('employment_type', $response_data['employment_type'])
            ->where('period_type', $response_data['period_type'])
            ->where('month', $response_data['month_of'])
            ->where('year', $response_data['year_of'])
            ->first();

        return response()->json([
            'message' => "Successfully Fetched.",
            'data' => new PayrollPeriodResource($payroll_period),
            "employee_generated" => count($employees),
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    public function salaryRate($basic_Salary)
    {
        $per_day = $basic_Salary / $this->Working_Days;

        // Calculate the per-hour rate
        $per_hour = $per_day / $this->Working_Hours;

        // Calculate the per-minute rate
        $per_minute = $per_hour / 60;

        // Calculate the per-week rate (assuming 5 workdays in a week)
        $per_week = $per_day * 5;

        // Return rates, rounded to 3 decimal places
        return [
            'weekly' => round($per_week, 2) ?? 0,
            'daily' => round($per_day, 2) ?? 0,
            'hourly' => round($per_hour, 2) ?? 0,
            'minutes' => round($per_minute, 2) ?? 0,
        ];
    }

    public function absentRate($number_of_absences, $daily_rate)
    {
        if ($number_of_absences >= 1) {
            return round($daily_rate * $number_of_absences, 2);
        }

        return 0;
    }

    public function undertimeRate($total_undertime, $minute_rate)
    {
        if ($total_undertime >= 1) {
            return round($total_undertime * $minute_rate, 2);
        }

        return 0;
    }

    public function netPay($base_salary, $total_present_days)
    {
        if ($total_present_days >= 1) {
            $rate = round($base_salary / $this->Working_Days, 2);
            return round($rate * $total_present_days, 2);
        }

        return 0;
    }

    public function getDateIntervals($from, $to)
    {
        $dates_Interval = [];
        $from = strtotime($from);
        $to = strtotime($to);
        while ($from <= $to) {
            $dates_Interval[] = date('Y-m-d', $from);
            $from = strtotime('+1 day', $from);
        }

        return $dates_Interval;
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

    /**
     * Helper function to determine exclusion reason
     */
    private function getExclusionReason($details)
    {
        if (isset($details['leave_applications'])) {
            if (isset($details['leave_applications']['leave_type']) === 'Study Leave') {
                return 'Study Leave' . $details['leave_applications']['from'] . "-" . $details['leave_applications']['to'];
            } elseif ($details['time_record']['net_pay'] < 5000) {
                return 'SALARY BELOW 5000 ' . $details['employment_type']['name'];
            }
        }
    }
}
