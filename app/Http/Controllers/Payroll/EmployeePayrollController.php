<?php

namespace App\Http\Controllers\Payroll;

use App\Data\EmployeePayrollData;
use App\Enums\PayrollType;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeePayrollRequest;
use App\Http\Resources\EmployeePayrollResource;
use App\Http\Resources\PaginationResource;
use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;
use App\Models\PayrollSummary;
use App\Services\EmployeePayrollService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see EmployeePayrollDocumentation
 *
 * included = [index, store, show]
 *
 * NOTE: Server-side payroll GENERATION (General / Night / Special) now lives in
 * PayrollGeneratorController (POST payroll/generate). This controller's store()
 * remains for persisting client-previewed REGULAR rows.
 */
class EmployeePayrollController extends Controller
{
    public function __construct(private EmployeePayrollService $service)
    {
        // Nothing
    }

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
}
