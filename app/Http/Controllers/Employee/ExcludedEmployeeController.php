<?php

namespace App\Http\Controllers\Employee;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeInformationResource;
use App\Http\Resources\ExcludedEmployeeResource;
use App\Models\EmployeeList;
use Illuminate\Http\Request;
use App\Models\ExcludedEmployee;
use Symfony\Component\HttpFoundation\Response;


class ExcludedEmployeeController extends Controller
{
    private $CONTROLLER_NAME = 'Excluded Employee';
    private $PLURAL_MODULE_NAME = 'excluded employees';
    private $SINGULAR_MODULE_NAME = 'excluded employee';

    public function index()
    {
        try {
            //return request()->processMonth['month'];
            $employee = ExcludedEmployee::with([
                'EmployeeList' => function ($query) {
                    $query->where('is_excluded', 1);
                }
            ])->where('month', request()->processMonth['month'])
                ->where('year', request()->processMonth['year'])
                ->where('is_removed', 0)
                ->get();


            return response()->json([
                'message' => "List retrieved successfully",
                'responseData' => $employee,
                'statusCode' => 200
            ]);
        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // store excluded employee
    public function store(Request $request)
    {
        $data = new ExcludedEmployee();
        $data->employee_list_id = $request->employee_list_id;
        $data->payroll_headers_id = $request->payroll_headers_id ?? null;
        $data->reason = json_encode($request->reason);
        $data->year = $request->year ?? $request->processMonth['year'];
        $data->month = $request->month ?? $request->processMonth['month'];
        $data->is_removed = $request->is_removed;
        $data->save();

        EmployeeList::where('id', $data->employee_list_id)->update(['is_excluded' => true]);

        // Helpers::registerSystemLogs($request, $data->id, true, 'Success in creating ' . $this->SINGULAR_MODULE_NAME . '.');
        return response()->json([
            'data' => new ExcludedEmployeeResource($data),
            'message' => "Data Successfully saved",
            'statusCode' => Response::HTTP_CREATED
        ], Response::HTTP_CREATED);

    }

    // change status to include (employee lists table) and remove employee from excluded list (excluded employees table)
    public function update(Request $request, ExcludedEmployee $excludedEmployee)
    {
        $excludedEmployee->update([
            'employee_list_id' => $request->employee_list_id,
            'month' => $request->month,
            'year' => $request->year,
            'reason' => json_encode($request->reason),
            'is_removed' => $request->is_removed,
        ]);

        // Helpers::registerSystemLogs($request, $data->id, true, 'Success in creating ' . $this->SINGULAR_MODULE_NAME . '.');
        return response()->json([
            'data' => new ExcludedEmployeeResource($excludedEmployee),
            'message' => "Data Successfully updated",
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    /**
     * Helper function to determine exclusion reason
     */
    private function getExclusionReason($details)
    {
        if ($details['employee']['leave_applications'] !== null) {
            if ($details['employee']['leave_applications']['leave_type'] === 'Study Leave') {
                return 'Study Leave';
            } elseif ($details['overall_net_salary'] < 5000) {
                return 'SALARY BELOW 5000 ' . $details['employee']['employment_type']['name'];
            }
            return $details['employee']['excluded']['status'];
        }
    }

    /**
     * Helper function to determine exclusion remarks
     */
    private function getExclusionRemarks($details)
    {
        if ($details['employee']['leave_applications'] !== null) {
            if ($details['employee']['leave_applications']['leave_type'] !== 'Study Leave') {
                return $details['employee']['excluded']['remarks'] ?? null;
            }
            return $details['leave_applications']['from'] . "-" . $details['leave_applications']['to'];
        }
    }
}
