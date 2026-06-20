<?php

namespace App\Http\Controllers\Employee;

use App\Data\ExcludedEmployeeData;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExcludedEmployeeRequest;
use App\Http\Resources\ExcludedEmployeeResource;
use App\Services\ExcludedEmployeeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see ExcludedEmployeeDocumentation
 * 
 * included = [index, store, update, destroy]
 */
class ExcludedEmployeeController extends Controller
{
    public function __construct(private ExcludedEmployeeService $service)
    {
        //nothing
    }

    public function index(Request $request)
    {
        $paginate = $request->boolean('paginate', false);

        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        $payrollPeriodId = $request->payroll_period_id;

        $data = $paginate === true ? $this->service->paginate($perPage, $page, $payrollPeriodId) : $this->service->getAll($payrollPeriodId);

        return response()->json([
            'responseData' => ExcludedEmployeeResource::collection($data),
            'message' => 'Data retrieved successfully.',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }

    public function store(ExcludedEmployeeRequest $request)
    {
        $dto = ExcludedEmployeeData::fromRequest($request);
        $this->service->create($dto);

        return response()->json([
            'message' => 'Data successfully saved.',
            'statusCode' => 201,
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $array = $request->validate([
            'payroll_period_id' => 'required|integer',
            'employee_id' => 'required|integer',
            'reason' => 'required|string',
            'is_removed' => 'required|boolean',
        ]);

        $this->service->update($id, $array);

        return response()->json([
            'message' => 'Data successfully updated.',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'message' => 'Data successfully deleted.',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }
}
