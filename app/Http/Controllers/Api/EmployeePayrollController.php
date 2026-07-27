<?php

namespace App\Http\Controllers\Api;

use App\Contract\EmployeePayrollInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeePayrollRequest;
use App\Http\Requests\UpdateEmployeePayrollRequest;
use App\Http\Resources\EmployeePayrollResource;
use App\Models\EmployeePayroll;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeePayrollController extends Controller
{
    public function __construct(private EmployeePayrollInterface $payrolls) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return EmployeePayrollResource::collection(
            $this->payrolls->getAll((int) $request->integer('payroll_period_id'))
        );
    }

    public function store(StoreEmployeePayrollRequest $request): EmployeePayrollResource
    {
        return new EmployeePayrollResource($this->payrolls->create($request->validated()));
    }

    public function show(EmployeePayroll $employee_payroll): EmployeePayrollResource
    {
        return new EmployeePayrollResource($employee_payroll->load(['employee', 'details']));
    }

    public function update(UpdateEmployeePayrollRequest $request, EmployeePayroll $employee_payroll): EmployeePayrollResource
    {
        return new EmployeePayrollResource($this->payrolls->update($employee_payroll->id, $request->validated()));
    }
}
