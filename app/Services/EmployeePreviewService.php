<?php

namespace App\Services;

use App\Http\Resources\EmployeePreviewResource;
use App\Http\Resources\PaginationResource;
use App\Models\PayrollPeriod;
use App\Support\NetPayProjection;
use App\Support\NetPayProjector;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Presents the payroll preview.
 *
 * This service is read-only. It used to copy the previous period's deductions
 * forward as a side effect of rendering, which advanced the paid counter on
 * term-based loans every time someone opened the screen. That copy now belongs
 * to DeductionCarryForward and runs at sync and posting time.
 *
 * The arithmetic moved out too. Working out what a period would pay is
 * NetPayProjector's job, because steps 4, 5 and 7 all need the same answer and
 * a second copy here is how they would come to disagree. What is left is
 * selecting, paginating and shaping for the API.
 */
class EmployeePreviewService
{
    public function __construct(private NetPayProjector $projector)
    {
        //
    }

    /**
     * One employee's projected pay.
     *
     * Basic pay used to be read from employee_time_records.basic_pay — a column
     * the migration comments out, so it does not exist. The value was always
     * null, and every figure this returned was short by the whole basic salary.
     * It comes from the projector now, like everything else.
     */
    public function find(int $employeeId, int $payrollPeriodId): ?array
    {
        $period = PayrollPeriod::findOrFail($payrollPeriodId);

        $projection = $this->projector->projectOne($period, $employeeId);

        if ($projection === null) {
            return null;
        }

        $employee = $projection->employee;
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
            'reason' => $projection->reasonLabel(),
            'status' => $employee->employeeTimeRecords->status ?? null,
            'payroll_records' => [
                'payroll_period_id' => $payrollPeriodId,
                'total_receivables' => $projection->totalReceivables,
                'total_deductions' => $projection->totalDeductions,
                'basic_pay' => $projection->basicPay,
                'gross_pay' => $projection->grossPay,
                'net_pay' => $projection->netPay,
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

    /**
     * The projections for a period, filtered by bucket and shaped for the
     * resource.
     *
     * 'all' keeps the included employees ahead of the excluded ones, which is
     * the order this endpoint has always returned.
     */
    private function classified(string $type, int $payrollPeriodId, array $selectedEmployeeIds): Collection
    {
        $period = PayrollPeriod::findOrFail($payrollPeriodId);

        $projections = $this->projector->project($period, $selectedEmployeeIds);

        $included = $projections->filter(fn (NetPayProjection $p) => $p->isIncluded());
        $excluded = $projections->filter(fn (NetPayProjection $p) => $p->isExcluded());

        $selected = match ($type) {
            'included' => $included,
            'excluded' => $excluded,
            default => $included->concat($excluded),
        };

        return $selected
            ->map(fn (NetPayProjection $p) => $p->toPreviewPayload())
            ->values();
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
