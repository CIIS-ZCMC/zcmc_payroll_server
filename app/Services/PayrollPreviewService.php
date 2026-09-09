<?php

namespace App\Services;

use App\Http\Resources\PaginationResource;
use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;

/**
 * Step 7: review what step 6 generated.
 *
 * Reads employee_payrolls. It does not recompute, on purpose — a preview that
 * derives its own figures can disagree with the rows that will actually be
 * posted, which is the whole class of bug the pipeline has been carrying.
 * What is on this screen is what is in the table.
 *
 * EmployeePreviewService is a different thing and stays: it projects what a
 * period *would* pay, which is what steps 4 and 5 need before anything has been
 * generated.
 */
class PayrollPreviewService
{
    public function __construct(private PayrollProcessService $processes)
    {
        //
    }

    /**
     * @return array{data: mixed, meta: mixed, summary: mixed, requires_recompute: bool, generated_at: mixed}
     */
    public function preview(PayrollPeriod $period, int $payrollType, int $perPage, int $page): array
    {
        $paginator = EmployeePayroll::with(['employee', 'employeeTimeRecord'])
            ->join('employees', 'employees.id', '=', 'employee_payrolls.employee_id')
            ->where('employee_payrolls.payroll_period_id', $period->id)
            ->orderBy('employees.last_name')
            ->select('employee_payrolls.*')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->getCollection()->map(fn (EmployeePayroll $row) => $this->line($row))->all(),
            'meta' => new PaginationResource($paginator),
            'summary' => $this->totals($period),

            // Steps 1 to 5 all change the inputs to the computation. When any of
            // them has run since the last generation these rows are stale, and
            // the run has to go back through step 6 before it is posted.
            'requires_recompute' => $this->processes->isDirty((int) $period->id, $payrollType),
            'generated_at' => $period->last_generated_at,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function line(EmployeePayroll $row): array
    {
        $employee = $row->employee;
        $area = json_decode($employee->assigned_area ?? '{}', true) ?? [];

        return [
            'id' => $employee->id,
            'employee_number' => $employee->employee_number,
            'full_name' => $employee->full_name,
            'designation' => $employee->designation,
            'assigned_area' => [
                'details' => [
                    'id' => $area['details']['id'] ?? null,
                    'name' => $area['details']['name'] ?? null,
                    'code' => $area['details']['code'] ?? null,
                ],
                'sector' => $area['sector'] ?? null,
            ],
            'payroll' => [
                'payroll_period_id' => $row->payroll_period_id,
                'employee_time_record_id' => $row->employee_time_record_id,
                'basic_pay' => $row->basic_pay,
                'total_receivables' => $row->total_receivables,
                'total_deductions' => $row->total_deductions,
                'gross_pay' => $row->gross_pay,
                'net_pay' => $row->net_pay,
                'first_half' => $row->first_half,
                'second_half' => $row->second_half,
                'currency' => 'PHP',
            ],
        ];
    }

    /**
     * Totalled from the same rows the page above shows, so the figure at the
     * bottom of the screen is the sum of what is on it.
     *
     * @return array<string, mixed>
     */
    private function totals(PayrollPeriod $period): array
    {
        $totals = EmployeePayroll::where('payroll_period_id', $period->id)
            ->selectRaw('
                COUNT(*) as employees,
                COALESCE(SUM(basic_pay), 0) as basic_pay,
                COALESCE(SUM(total_receivables), 0) as total_receivables,
                COALESCE(SUM(total_deductions), 0) as total_deductions,
                COALESCE(SUM(gross_pay), 0) as gross_pay,
                COALESCE(SUM(net_pay), 0) as net_pay
            ')
            ->first();

        return [
            'employees' => (int) $totals->employees,
            'basic_pay' => round((float) $totals->basic_pay, 2),
            'total_receivables' => round((float) $totals->total_receivables, 2),
            'total_deductions' => round((float) $totals->total_deductions, 2),
            'gross_pay' => round((float) $totals->gross_pay, 2),
            'net_pay' => round((float) $totals->net_pay, 2),
            'currency' => 'PHP',
        ];
    }
}
