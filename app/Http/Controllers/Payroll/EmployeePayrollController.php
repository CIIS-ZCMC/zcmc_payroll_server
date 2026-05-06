<?php

namespace App\Http\Controllers\Payroll;

use App\Data\EmployeePayrollData;
use App\Enums\PayrollType;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeePayrollRequest;
use App\Http\Resources\EmployeePayrollResource;
use App\Http\Resources\PaginationResource;
use App\Models\EmployeePayroll;
use App\Models\GeneralPayroll;
use App\Models\PayrollPeriod;
use App\Models\PayrollSummary;
use App\Services\EmployeePayrollService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use DateInterval;

class EmployeePayrollController extends Controller
{

    public function __construct(private EmployeePayrollService $service)
    {
        // Nothing
    }

    /**
     * @OA\Get(
     *     path="/api/employee-payrolls",
     *     summary="Get employee payroll",
     *     tags={"Employee Payroll"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Default employee payroll response",
     *         @OA\JsonContent(
     *             type="string",
     *             example="generated employee payroll"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The selected employees field is required."),
     *             @OA\Property(property="errors", type="object", example={"selected_employees": {"The selected employees field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page;
        $page = $request->page;

        $data = $this->service->paginate($perPage, $page);

        return response()->json([
            'data' => EmployeePayrollResource::collection($data),
            'meta' => new PaginationResource($data),
            'message' => 'Data retrieved successfully.',
            'success' => true,
        ], Response::HTTP_OK);
    }

    public function store(EmployeePayrollRequest $request)
    {
        switch ($request->payroll_type) {
            case PayrollType::NIGHT:
                // $this->processNightDifferential();
                break;

            case PayrollType::REGULAR:
                $dto = EmployeePayrollData::collection($request->employee_payroll)->toArray();
                $this->service->updateOrInsert($dto);
                break;

            default:

                break;
        }

        return response()->json([
            'message' => 'Data successfully saved.',
            'success' => true,
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        // $general_payroll = GeneralPayroll::find($id);
        $general_payroll = PayrollSummary::find($id);

        if (!$general_payroll) {
            return response()->json([
                'message' => 'No data found for the specified payroll period.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        $payroll_period_id = $general_payroll->payroll_period_id;
        $payroll_period = PayrollPeriod::find($payroll_period_id);

        if (!$payroll_period) {
            return response()->json(['message' => 'Payroll period not found', 'statusCode' => 404], Response::HTTP_NOT_FOUND);
        }

        $data = EmployeePayroll::with([
            'employee',
            'employee.employeeDeductions',
            'employee.employeeReceivables',
            'employeeTimeRecord',
            'employeeTimeRecord.employeeComputedSalary',
            'payrollPeriod',
        ])->where('payroll_period_id', $payroll_period->id)->get();

        return response()->json([
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
            'responseData' => EmployeePayrollResource::collection($data),
        ], Response::HTTP_OK);
    }


    private function CalculateNightDifferential($totalNightDutyHours, $monthlyRate)
    {
        // $nightHours = $employeeList->getTimeRecords->total_night_duty_hours;
        // $nd_Rate = floor($monthly_rate * 0.005682 * 100) / 100;
        // $nd_Twenty_Percent = number_format($nd_Rate * 0.2, 2, '.', '');
        // $Accumulated_Amount_Night_Differential = number_format($nightHours * $nd_Twenty_Percent, 2, '.', '');
        // return Helpers::customRound($tempNetSalary + $Accumulated_Amount_Night_Differential);

        $totalAccumulatedND = 0.00;
        $nightdiffRate = floor($monthlyRate * 0.005682 * 100) / 100;
        $nightDifferentialTwentyPercentRate = floor($nightdiffRate * 0.2 * 100) / 100;
        $totalAccumulatedND = floor($totalNightDutyHours * $nightDifferentialTwentyPercentRate * 100) / 100;


        return $totalAccumulatedND;
    }

    public function getNightHours($dtrRecord, $scheduleStartHour = 22, $scheduleEndHour = 6)
    {
        /**
         * Calculate hours within the given schedule
         * 
         * @param object $dtrRecord Object containing DTR information
         * @param int $scheduleStartHour Starting hour of schedule
         * @param int $scheduleEndHour Ending hour of schedule
         * @return array Array with dtr_date and total_night_hours
         */

        $results = [];



        if (!empty($dtrRecord->first_in) && !empty($dtrRecord->first_out)) {
            $firstIn = new DateTime($dtrRecord->first_in);
            $firstOut = new DateTime($dtrRecord->first_out);

            $shiftHours = $this->calculateHoursInSchedule($firstIn, $firstOut, $scheduleStartHour, $scheduleEndHour);
            $results = array_merge($results, $shiftHours);
        }

        if (!empty($dtrRecord->second_in) && !empty($dtrRecord->second_out)) {
            $secondIn = new DateTime($dtrRecord->second_in);
            $secondOut = new DateTime($dtrRecord->second_out);
            $shiftHours = $this->calculateHoursInSchedule($secondIn, $secondOut, $scheduleStartHour, $scheduleEndHour);
            $results = array_merge($results, $shiftHours);
        }

        $combinedResults = [];
        foreach ($results as $result) {
            $date = $result['dtr_date'];
            if (!isset($combinedResults[$date])) {
                $combinedResults[$date] = [
                    'dtr_date' => $date,
                    'total_night_hours' => 0
                ];
            }
            $combinedResults[$date]['total_night_hours'] += $result['total_night_hours'];
        }

        return array_values($combinedResults);
    }

    private function calculateHoursInSchedule($start, $end, $scheduleStartHour, $scheduleEndHour)
    {
        $hoursByDate = [];

        $current = clone $start;
        $interval = new DateInterval('PT1M');

        while ($current < $end) {
            $currentDate = $current->format('Y-m-d');
            $currentHour = (int) $current->format('H');
            $isInSchedule = false;

            if ($scheduleStartHour < $scheduleEndHour) {
                $isInSchedule = ($currentHour >= $scheduleStartHour && $currentHour < $scheduleEndHour);
            } else {
                $isInSchedule = ($currentHour >= $scheduleStartHour || $currentHour < $scheduleEndHour);
            }

            if ($isInSchedule) {
                if (!isset($hoursByDate[$currentDate])) {
                    $hoursByDate[$currentDate] = [
                        'dtr_date' => $currentDate,
                        'total_night_hours' => 0
                    ];
                }
                $hoursByDate[$currentDate]['total_night_hours'] += (1 / 60);
            }
            $current->add($interval);
        }
        foreach ($hoursByDate as &$entry) {
            $entry['total_night_hours'] = round($entry['total_night_hours'], 2);
        }

        return array_values($hoursByDate);
    }

    // private function processNightDifferential()
    // {
    //     $payrollPeriod = new PayrollPeriodController();
    //     $response = $payrollPeriod->index(request())->getData(true);
    //     $activePeriod = PayrollPeriod::find($response['data']['id']);
    //     $employees = [];
    //     $nightDifferentialAmount = 0;
    //     foreach ($activePeriod->employeePayroll as $employee) {
    //         $timeRecord = $employee->employeeTimeRecord;
    //         $nightDifferential = json_decode($timeRecord->night_duties);
    //         $employeeInfo = $employee->employee;
    //         //Processing only employees with night differential
    //         if ($nightDifferential) {

    //             $schedule = json_decode($timeRecord->schedules);
    //             $nightDiffRecord = [];

    //             foreach ($nightDifferential as $dtrRecord) {
    //                 $timeshifts = collect(array_values(array_filter($schedule, function ($schedule) use ($dtrRecord) {
    //                     return $schedule->date == $dtrRecord->dtr_date;
    //                 })))->first();

    //                 if ($timeshifts) {
    //                     $timeshift = $timeshifts->time_shift;
    //                     $nightHours = $this->getNightHours(
    //                         $dtrRecord,
    //                         Carbon::createFromFormat('H:i:s', $timeshift->first_in)->hour,
    //                         Carbon::createFromFormat('H:i:s', $timeshift->first_out)->hour
    //                     );
    //                     $nightDiffRecord = array_merge($nightDiffRecord, $nightHours);
    //                 }

    //             }

    //             $totalNightHours = array_reduce($nightDiffRecord, function ($carry, $item) {
    //                 return $carry + $item['total_night_hours'];
    //             }, 0);

    //             $nightDifferentialAmount = $this->CalculateNightDifferential($totalNightHours, decrypt($employeeInfo->employeeSalary->base_salary));

    //             $employees[] = [
    //                 'EmployeeInfo' => $employeeInfo,
    //                 'TimeRecordID' => $timeRecord->id,
    //                 'NightDifferentialAmount' => $nightDifferentialAmount
    //             ];
    //             //Return list here for employee's with night differential

    //         }
    //     }


    //     //create new Payroll Period . for the current active month and year
    //     //create generate payroll , total of night differential
    //     //create employee employee payroll


    //     $user = request()->user;
    //     // $user = (object)['employee_id'=>2022090251, 'name'=>'Reenjay Caimor'];

    //     // To do . check for existing payroll period if its locked , then do not allow to proceed 



    //     $existingNDFPayrollPeriod = PayrollPeriod::where('month', $activePeriod->month)
    //         ->where('year', $activePeriod->year)
    //         ->where('employment_type', 'permanent')
    //         ->where('period_type', 'full_month')
    //         ->where('payroll_type', 'Night Differential')
    //         ->whereNotNull('locked_at')
    //         ->first();


    //     if ($existingNDFPayrollPeriod) {
    //         return response()->json([
    //             'message' => 'Payroll period already locked.',
    //             'statusCode' => 400,
    //         ], Response::HTTP_BAD_REQUEST);
    //     }

    //     $payrollPeriod = PayrollPeriod::firstOrCreate([
    //         'month' => $activePeriod->month,
    //         'year' => $activePeriod->year,
    //         'payroll_type' => 'Night Differential',
    //         'employment_type' => $activePeriod->employment_type,
    //         'period_type' => "full_month",
    //         'period_start' => $activePeriod->period_start,
    //         'period_end' => $activePeriod->period_end,
    //         'days_of_duty' => $activePeriod->days_of_duty,
    //         'is_special' => $activePeriod->is_special,
    //         'posted_at' => $activePeriod->posted_at,
    //         'last_generated_at' => $activePeriod->last_generated_at,
    //         'locked_at' => $activePeriod->locked_at,
    //     ], [
    //         'is_active' => 0
    //     ]);

    //     $total_Gross_ND = array_reduce($employees, function ($carry, $item) {
    //         return $carry + $item['NightDifferentialAmount'];
    //     }, 0);


    //     $generalPayroll = GeneralPayroll::updateOrCreate(
    //         [
    //             'payroll_period_id' => $payrollPeriod->id,
    //             'month' => $activePeriod->month,
    //             'year' => $activePeriod->year,
    //             'generated_by_id' => $user->employee_id,
    //             'generated_by_name' => $user->name,
    //         ],
    //         [
    //             'payroll_period_id' => $payrollPeriod->id,
    //             'total_employees' => count($employees),
    //             'total_deductions' => 0,
    //             'total_receivables' => 0,
    //             'total_gross' => $total_Gross_ND,
    //             'total_net' => $total_Gross_ND,
    //         ]
    //     );
    //     //Employee Payroll - Night Differential
    //     foreach ($employees as $employee) {
    //         $EmployeeInfo = $employee['EmployeeInfo'];
    //         $employee_time_record_id = $employee['TimeRecordID'];
    //         $NightDifferentialAmount = $employee['NightDifferentialAmount'];
    //         $employee_id = $EmployeeInfo['id'];
    //         $payroll_period_id = $payrollPeriod->id;

    //         //check if NDF of employee exists within the month and year 
    //         //- do not allow to add duplicates
    //         //this is for NDF only

    //         EmployeePayroll::updateOrCreate(
    //             [
    //                 'employee_id' => $employee_id,
    //                 'payroll_period_id' => $payroll_period_id,
    //             ],
    //             [
    //                 'month' => $activePeriod->month,
    //                 'year' => $activePeriod->year,
    //                 'gross_salary' => $NightDifferentialAmount,
    //                 'total_deductions' => 0,
    //                 'total_receivables' => 0,
    //                 'net_pay' => $NightDifferentialAmount,
    //                 'deleted_at' => null,
    //                 'employee_time_record_id' => $employee_time_record_id,
    //             ]
    //         );

    //     }

    //     return response()->json([
    //         'message' => 'Night Differential processed successfully',
    //         'payrollID' => $payrollPeriod->id,
    //         'statusCode' => 200
    //     ]);

    // }


}
