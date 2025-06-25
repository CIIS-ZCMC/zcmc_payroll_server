<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeductionRequest;
use App\Http\Resources\DeductionResource;
use App\Models\Deduction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeductionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'responseData' => DeductionResource::collection(Deduction::whereNull('deleted_at')->get()),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeductionRequest $request)
    {
        $validate = $request->validated();
        $validate_code = Deduction::whereNull('deleted_at')->where('code', $validate['code'])->first();

        if ($validate_code) {
            return response()->json(['message' => 'Code already exist'], Response::HTTP_FOUND);
        }

        if ($validate['type'] === null) {
            if ($validate['percent_value'] !== null) {
                $validate['type'] = 'percentage';
            } elseif ($validate['fixed_amount'] !== null) {
                $validate['type'] = 'fixed';
            }
        }

        $data = Deduction::create($validate);

        return response()->json([
            'responseData' => new DeductionResource($data),
            'message' => "Data Successfully saved",
            'statusCode' => 200
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $payroll_period_id = $request->payroll_period_id;

        $data = Deduction::with([
            'employeeDeductions' => function ($query) use ($payroll_period_id) {
                $query->where('payroll_period_id', $payroll_period_id);
            },
            'employeeDeductions.employee'
        ])->where('id', $id)->first();

        if (!$data) {
            return response()->json([
                'message' => "Data not found"
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new DeductionResource($data),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
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
        $data = Deduction::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => "Data not found"
            ], Response::HTTP_NOT_FOUND);
        }

        // Check if code already exists in other deductions
        if ($request->has('code')) {
            $existing = Deduction::whereNull('deleted_at')
                ->where('code', $request->input('code'))
                ->where('id', '!=', $id)
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Code already exist'
                ], Response::HTTP_FOUND);
            }
        }

        $data->update($request->all());

        return response()->json([
            'responseData' => new DeductionResource($data),
            'message' => "Data Successfully updated",
            'statusCode' => 200
        ], Response::HTTP_OK);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Deduction::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => "Data not found"
            ], Response::HTTP_NOT_FOUND);
        }

        $data->delete();

        return response()->json([
            'message' => "Data Successfully deleted",
            'statusCode' => 200
        ], Response::HTTP_OK);

    }

    // public function stop(Request $request, $id)
    // {
    //     try {
    //         $data = Deduction::findOrFail($id);
    //         $data->status = $request->status;
    //         $data->stopped_at = Carbon::now();
    //         $data->update();

    //         if ($data != null) {
    //             $deductionTrailController = new DeductionTrailController();
    //             $deductionTrailController->store($request);
    //         }

    //         // Helpers::registerSystemLogs($request, $id, true, 'Success in delete ' . $this->SINGULAR_MODULE_NAME . '.');
    //         return response()->json(['message' => "Data Successfully stop", 'statusCode' => Response::HTTP_OK], Response::HTTP_OK);

    //     } catch (\Throwable $th) {

    //         Helpers::errorLog($this->CONTROLLER_NAME, 'stop', $th->getMessage());
    //         return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }

    // public function clearEmployeeDeductions($id)
    // {
    //     try {
    //         $dateArray = request()->processMonth;

    //         // Construct the date strings from the array
    //         $dateToString = $dateArray['year'] . '-' . $dateArray['month'] . '-' . $dateArray['JOtoPeriod'];
    //         $dateFromString = $dateArray['year'] . '-' . $dateArray['month'] . '-' . $dateArray['JOfromPeriod'];

    //         // Create DateTime objects from the generated date strings
    //         $payrollDateTo = new DateTime($dateToString);
    //         $payrollDateFrom = new DateTime($dateFromString);

    //         // Check if JOtoPeriod is zero
    //         if ($dateArray['JOtoPeriod'] === 0) {
    //             // Set payrollDateTo to the last day of the month
    //             $payrollDateTo = (new DateTime("$dateArray[year]-$dateArray[month]-01"))->modify('last day of this month');
    //         }

    //         // Check if JOfromPeriod is zero
    //         if ($dateArray['JOfromPeriod'] === 0) {
    //             // Set payrollDateFrom to the first day of the month
    //             $payrollDateFrom = (new DateTime("$dateArray[year]-$dateArray[month]-01"));
    //         }

    //         $deduction = Deduction::find($id);
    //         if (!$deduction) {
    //             return response()->json([
    //                 'message' => 'Deduction not found'
    //             ], Response::HTTP_NOT_FOUND);
    //         }
    //         $deduction->getImports()
    //             ->whereBetween('payroll_date', [$payrollDateFrom, $payrollDateTo])
    //             ->delete();
    //         // $deduction->employeeDeductions()->update(['willDeduct' => null]);
    //         $deduction->employeeDeductions()
    //             ->whereDate('created_at', Carbon::today())
    //             ->delete();

    //         return response()->json(['Message' => "Successfuly Cleared all willDeduct list " . $id, 'statusCode' => Response::HTTP_OK], Response::HTTP_OK);
    //     } catch (\Throwable $th) {
    //         Helpers::errorLog($this->CONTROLLER_NAME, 'clearEmployeeDeductions', $th->getMessage());
    //         return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }
}
