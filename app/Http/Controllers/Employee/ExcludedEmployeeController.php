<?php

namespace App\Http\Controllers\Employee;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeInformationResource;
use App\Http\Resources\ExcludedEmployeeResource;
use App\Models\EmployeeList;
use Illuminate\Http\Request;
use App\Models\ExcludedEmployee;
use Illuminate\Http\Response;


class ExcludedEmployeeController extends Controller
{
    private $CONTROLLER_NAME = 'Excluded Employee';
    private $PLURAL_MODULE_NAME = 'excluded employees';
    private $SINGULAR_MODULE_NAME = 'excluded employee';

    public function index()
    {

        //return request()->processMonth['month'];
        $employeeList = ExcludedEmployee::with(['EmployeeList'])->where('month', request()->processMonth['month'])
            ->where('year', request()->processMonth['year'])
            ->where('is_removed', 0)
            ->get();
        return response()->json([
            'message' => "List retrieved successfully",
            'responseData' => $employeeList,
            'statusCode' => 200
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = new ExcludedEmployee();
            $data->employee_list_id = $request->employee_list_id;
            $data->payroll_headers_id = $request->payroll_headers_id ? $request->payroll_headers_id : null;
            $data->reason = json_encode($request->reason);
            $data->year = $request->processMonth['year'];
            $data->month = $request->processMonth['month'];
            $data->is_removed = $request->is_removed;
            $data->save();

            EmployeeList::where('id', $data->employee_list_id)->update(['is_excluded' => true]);

            // Helpers::registerSystemLogs($request, $data->id, true, 'Success in creating ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json([
                // 'data' => new ExcludedEmployeeResource($data),
                'message' => "Data Successfully saved",
                'statusCode' => Response::HTTP_OK
            ], Response::HTTP_OK);


        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'store', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
