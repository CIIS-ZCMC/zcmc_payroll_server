<?php

namespace App\Services;

use App\Http\Resources\EmployeePreviewResource;
use App\Http\Resources\PaginationResource;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;
use App\Support\DeductionCarryForward;
use App\Support\PayrollCodes;
use App\Support\PayrollPeriodResolver;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Builds the payroll preview.
 *
 * This service is read-only. It used to copy the previous period's deductions
 * forward as a side effect of rendering, which advanced the paid counter on
 * term-based loans every time someone opened the screen. That copy now belongs
 * to DeductionCarryForward and runs at sync and posting time.
 */
class EmployeePreviewService
{
    public function __construct(private PayrollPeriodResolver $periods)
    {
        //
    }

    public function find(int $employeeId, int $payrollPeriodId)
    {
        $employee = Employee::with([
            'employeeTimeRecords' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId),
            'employeeReceivables' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId),
            'employeeDeductions' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId),
            'excludedEmployees' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId),
        ])->findOrFail($employeeId);

        // employeeTimeRecords is a hasOne, so this is a model or null. Calling
        // ->first() on it would fall through Model::__call to a fresh query and
        // return the first time record in the whole table.
        $timeRecord = $employee->employeeTimeRecords;

        $computedSalary = $timeRecord->basic_pay ?? 0;
        $totalReceivables = $employee->employeeReceivables->sum('amount');
        $totalDeductions = $employee->employeeDeductions->sum('amount');

        $grossPay = $computedSalary + $totalReceivables;
        $netPay = $grossPay - $totalDeductions;

        $area = json_decode($employee->assigned_area, true);

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
            'reason' => $this->exclusionReason($employee),
            'status' => $timeRecord->status ?? null,
            'payroll_records' => [
                'payroll_period_id' => $payrollPeriodId,
                'total_receivables' => $totalReceivables,
                'total_deductions' => $totalDeductions,
                'basic_pay' => $computedSalary,
                'gross_pay' => $grossPay,
                'net_pay' => $netPay,
                'currency' => 'PHP',
            ],
        ];
    }

    public function getAll(string $type, int $payrollPeriodId, array $selectedEmployeeIds)
    {
        return [
            'data' => EmployeePreviewResource::collection(
                $this->classified($type, $payrollPeriodId, $selectedEmployeeIds)
            ),
            'meta' => null,
        ];
    }

    public function preview(string $type, int $payrollPeriodId, array $selectedEmployeeIds, int $perPage, int $page)
    {
        $paginator = $this->paginate(
            $this->classified($type, $payrollPeriodId, $selectedEmployeeIds),
            $perPage,
            $page
        );

        return [
            'data' => EmployeePreviewResource::collection($paginator),
            'meta' => new PaginationResource($paginator),
        ];
    }

    private function classified(string $type, int $payrollPeriodId, array $selectedEmployeeIds): Collection
    {
        $period = PayrollPeriod::findOrFail($payrollPeriodId);

        $employees = $this->fetchEmployees($period, $selectedEmployeeIds);

        [$included, $excluded] = $this->calculateAndClassify($employees, $period);

        return match ($type) {
            'included' => collect($included),
            'excluded' => collect($excluded),
            default => collect([...$included, ...$excluded]),
        };
    }

    private function fetchEmployees(PayrollPeriod $period, array $selectedEmployeeIds): Collection
    {
        $payrollPeriodId = $period->id;

        // Which period the deductions are read from is decided once, here,
        // rather than by a count() query inside an eager-load closure.
        [$deductionPeriodId, $isFallback] = $this->deductionSource($period);

        $query = Employee::with([
            'employeeSalary' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId),

            'employeeComputedSalary' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId),

            'employeeTimeRecords' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId)
                ->where('is_active', true),

            'employeeReceivables' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId),

            // When falling back to the previous period, show what would
            // actually be carried forward — not the stopped, finished and
            // expired rows that will be left behind.
            'employeeDeductions' => function ($q) use ($deductionPeriodId, $isFallback) {
                $q->where('payroll_period_id', $deductionPeriodId);

                if ($isFallback) {
                    DeductionCarryForward::scopeInheritable($q);
                }
            },

            'excludedEmployees' => fn ($q) => $q->where('payroll_period_id', $payrollPeriodId),
        ])->orderBy('last_name');

        if (! empty($selectedEmployeeIds)) {
            $query->whereIn('id', $selectedEmployeeIds);
        }

        return $query->get();
    }

    /**
     * Deductions come from this period once it has any, and from the previous
     * period until then.
     *
     * @return array{0: int, 1: bool}  The period id, and whether it is the fallback.
     */
    private function deductionSource(PayrollPeriod $period): array
    {
        $hasOwn = EmployeeDeduction::where('payroll_period_id', $period->id)->exists();

        if ($hasOwn) {
            return [(int) $period->id, false];
        }

        $previous = $this->periods->previousPeriod($period);

        return $previous
            ? [(int) $previous->id, true]
            : [(int) $period->id, false];
    }

    private function calculateAndClassify(Collection $employees, PayrollPeriod $period): array
    {
        $included = [];
        $excluded = [];

        $threshold = PayrollCodes::netPayExclusionThreshold();

        // The locked first-half amounts, read once for everyone rather than
        // once per employee inside the loop.
        $lockedFirstHalves = $this->lockedFirstHalves($period, $employees);

        foreach ($employees as $employee) {
            $record = $employee->employeeTimeRecords;

            if (! $record) {
                continue;
            }

            $basic = $employee->employeeComputedSalary->basic_pay ?? 0;
            $receivables = round($employee->employeeReceivables->sum('amount'), 2);
            $deductions = round($employee->employeeDeductions->sum('amount'), 2);

            $gross = round($basic + $receivables, 2);
            $net = round($gross - $deductions, 2);

            if ($period->period_type === 'first_half') {
                $firstHalf = round(floor($net / 2), 2);
            } else {
                $firstHalf = $lockedFirstHalves[$employee->id] ?? 0;
            }

            $secondHalf = round($net - $firstHalf, 2);

            $payload = [
                'employee' => $employee,
                'payroll' => [
                    'payroll_period_id' => $record->payroll_period_id,
                    'employee_time_record_id' => $record->id,
                    'basic_pay' => $basic,
                    'total_receivables' => $receivables,
                    'total_deductions' => $deductions,
                    'gross_pay' => $gross,
                    'net_pay' => $net,
                    'first_half' => $firstHalf,
                    'second_half' => $secondHalf,
                ],
            ];

            // An employee flagged out for this period is excluded whatever they
            // earn; below-threshold pay excludes the rest.
            if ($employee->excludedEmployees->isNotEmpty() || $net < $threshold) {
                $excluded[] = $payload;
            } else {
                $included[] = $payload;
            }
        }

        return [$included, $excluded];
    }

    /**
     * @return array<int, float>  Keyed by employee id.
     */
    private function lockedFirstHalves(PayrollPeriod $period, Collection $employees): array
    {
        if ($period->period_type === 'first_half' || $employees->isEmpty()) {
            return [];
        }

        $firstHalfPeriod = $this->periods->previousPeriod($period);

        if (! $firstHalfPeriod) {
            return [];
        }

        return EmployeePayroll::where('payroll_period_id', $firstHalfPeriod->id)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->pluck('first_half', 'employee_id')
            ->map(fn ($amount) => (float) $amount)
            ->all();
    }

    private function exclusionReason(Employee $employee): string
    {
        return $employee->excludedEmployees->first()->reason ?? 'Salary Below Threshold';
    }

    private function paginate(Collection $data, int $perPage, int $page): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $data->forPage($page, $perPage)->values(),
            $data->count(),
            $perPage,
            $page,
        );
    }
}
