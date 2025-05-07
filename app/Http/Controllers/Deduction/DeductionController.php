<?php

namespace App\Http\Controllers\Deduction;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeductionRequest;
use App\Http\Resources\DeductionResource;
use App\Models\Deduction;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeSalary;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class DeductionController extends Controller
{
    private $CONTROLLER_NAME = 'Deduction';
    private $PLURAL_MODULE_NAME = 'deductions';
    private $SINGULAR_MODULE_NAME = 'deduction';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return response()->json(['responseData' => DeductionResource::collection(Deduction::all())], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeductionRequest $request)
    {
        try {
            $amount = null;
            $percentage = null;
            $deductionData = $request->all();

            // Encode the 'designation' array to JSON
            if (isset($deductionData['designation']) && is_array($deductionData['designation'])) {
                $deductionData['designation'] = json_encode($deductionData['designation']);
            }

            // Decode the JSON string into a PHP array
            $decoded_condition = json_decode($request->condition, true); // true to get an associative array

            // Check if decoding was successful and count the conditions
            if ($decoded_condition !== null && is_array($decoded_condition)) {
                $count_condition = count($decoded_condition);
            } else {
                $count_condition = 0; // Handle the case where JSON is invalid or decoding failed
            }

            if ($count_condition === 1) {
                if ($decoded_condition['condition1']['charge_basis'] === 'percentage') {
                    $percentage = $decoded_condition['condition1']['charge_value']; // Store in $percentage
                } elseif ($decoded_condition['condition1']['charge_basis'] === 'amount') {
                    $amount = $decoded_condition['condition1']['charge_value']; // Store in $amount
                }

                // Merge the amounts into $deductionData to pass to the create method
                $deductionData = array_merge($deductionData, ['amount' => $amount, 'percentage' => $percentage]);
            }

            // Create the deduction entry with the updated data
            $data = Deduction::create($deductionData);

            // if (in_array('All Designation', $request->designation)) {
            //     // If "All Designation" is selected, store the deduction as is
            //     $data = Deduction::create($request->all());
            // } else {
            //     // Loop through each specific designation and create a separate deduction entry for each
            //     foreach ($request->designation as $designation) {
            //         // Clone the request data and set the specific designation for each entry
            //         $deductionData = $request->all();
            //         $deductionData['designation'] = $designation;

            //         // Save the deduction entry
            //         $data = Deduction::create($deductionData);
            //     }
            // }
            // Helpers::registerSystemLogs($request, $data->id, true, 'Success in creating ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json(['data' => new DeductionResource($data), 'message' => "Successfully saved", 'statusCode' => Response::HTTP_OK], Response::HTTP_OK);
        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'store', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = Deduction::findOrFail($id);
            return response()->json(['data' => new DeductionResource($data)], Response::HTTP_OK);


        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'show', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            $amount = null;
            $percentage = null;
            $deductionData = $request->all();

            // Retrieve the deduction record
            $deduction = Deduction::findOrFail($id);

            if (!$deduction) {
                return response()->json(['message' => 'No record found.', 'statusCode' => Response::HTTP_NOT_FOUND]);
            }

            // Encode the 'designation' array to JSON if it's provided
            if (isset($deductionData['designation']) && is_array($deductionData['designation'])) {
                $deductionData['designation'] = json_encode($deductionData['designation']);
            }

            // Decode the 'condition' JSON string into a PHP array
            $decoded_condition = json_decode($request->condition, true); // true for associative array

            // Check if decoding was successful and count the conditions
            if ($decoded_condition !== null && is_array($decoded_condition)) {
                $count_condition = count($decoded_condition);
            } else {
                $count_condition = 0; // Handle invalid JSON
            }

            if ($count_condition === 1) {
                if ($decoded_condition['condition1']['charge_basis'] === 'percentage') {
                    $percentage = $decoded_condition['condition1']['charge_value'];
                } elseif ($decoded_condition['condition1']['charge_basis'] === 'amount') {
                    $amount = $decoded_condition['condition1']['charge_value'];
                }
                // Merge the amounts into $deductionData to update the record
                $deductionData = array_merge($deductionData, ['amount' => $amount, 'percentage' => $percentage]);
            }

            // Update the deduction record with new data
            $deduction->update($deductionData);


            // Helpers::registerSystemLogs($request, $id, true, 'Success in updating ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json(['data' => new DeductionResource($deduction), 'message' => "Data Successfully update", 'statusCode' => Response::HTTP_OK], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'update', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $data = Deduction::findOrFail($id);
            $data->delete();

            // Helpers::registerSystemLogs($request, $id, true, 'Success in delete ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json(['message' => "Data Successfully deleted"], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'destroy', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function stop(Request $request, $id)
    {
        try {
            $data = Deduction::findOrFail($id);
            $data->status = $request->status;
            $data->stopped_at = Carbon::now();
            $data->update();

            if ($data != null) {
                $deductionTrailController = new DeductionTrailController();
                $deductionTrailController->store($request);
            }

            // Helpers::registerSystemLogs($request, $id, true, 'Success in delete ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json(['message' => "Data Successfully stop", 'statusCode' => Response::HTTP_OK], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'stop', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getEmploymentType()
    {
        try {
            $data = DB::connection('mysql2')->select('SELECT * FROM employment_types');
            return response()->json(['responseData' => $data], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'getEmploymentType', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getDesignation()
    {
        try {
            $data = DB::connection('mysql2')->select('SELECT * FROM designations');
            return response()->json(['responseData' => $data], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'getDesignation', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getArea()
    {
        try {
            $data = Helpers::getAllArea();
            return response()->json(['responseData' => $data], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'getArea', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getSalaryGrade()
    {
        try {
            $data = DB::connection('mysql2')->select('SELECT * FROM salary_grades');
            return response()->json(['responseData' => $data], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'getSalaryGrade', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function clearEmployeeDeductions($id)
    {
        try {
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

            $deduction = Deduction::find($id);
            if (!$deduction) {
                return response()->json([
                    'message' => 'Deduction not found'
                ], Response::HTTP_NOT_FOUND);
            }
            $deduction->getImports()
                ->whereBetween('payroll_date', [$payrollDateFrom, $payrollDateTo])
                ->delete();
            // $deduction->employeeDeductions()->update(['willDeduct' => null]);
            $deduction->employeeDeductions()
                ->whereDate('created_at', Carbon::today())
                ->delete();

            return response()->json(['Message' => "Successfuly Cleared all willDeduct list " . $id, 'statusCode' => Response::HTTP_OK], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Helpers::errorLog($this->CONTROLLER_NAME, 'clearEmployeeDeductions', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
