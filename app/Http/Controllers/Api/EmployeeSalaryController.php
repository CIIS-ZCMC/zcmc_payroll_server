<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeSalaryRequest;
use App\Http\Requests\UpdateEmployeeSalaryRequest;
use App\Http\Resources\EmployeeSalaryResource;
use App\Models\EmployeeSalary;
use App\Services\EmployeeSalaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeSalaryController extends Controller
{
    public function __construct(private EmployeeSalaryService $service) {}

    public function store(StoreEmployeeSalaryRequest $request): EmployeeSalaryResource
    {
        return new EmployeeSalaryResource($this->service->save($request->validated()));
    }

    public function update(UpdateEmployeeSalaryRequest $request, EmployeeSalary $employee_salary): EmployeeSalaryResource
    {
        return new EmployeeSalaryResource($this->service->update($employee_salary->id, $request->validated()));
    }

    public function import(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.employee_id' => ['required', 'exists:employees,id'],
            'rows.*.payroll_period_id' => ['required', 'exists:payroll_periods,id'],
            'rows.*.employment_type' => ['required', 'in:permanent,contractual,temporary'],
            'rows.*.base_salary' => ['required', 'numeric', 'min:0'],
            'rows.*.salary_grade' => ['nullable', 'integer'],
            'rows.*.salary_step' => ['nullable', 'integer'],
            'rows.*.is_active' => ['nullable', 'boolean'],
        ]);

        return response()->json(['imported' => $this->service->import($validated['rows'])]);
    }
}
