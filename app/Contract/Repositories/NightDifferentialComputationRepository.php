<?php

namespace App\Contract\Repositories;

use App\Contract\NightDifferentialComputationInterface;
use App\Models\EmployeeNightDiffComputation;
use App\Models\EmployeeNightDuties;
use Illuminate\Support\Collection;

class NightDifferentialComputationRepository implements NightDifferentialComputationInterface
{
    public function __construct(
        private EmployeeNightDiffComputation $computation,
        private EmployeeNightDuties $nightDuties
    ) {
        //
    }

    public function getByPeriod(int $payrollPeriodId, ?int $employeeId = null): Collection
    {
        $query = $this->computation->with('employee')
            ->where('payroll_period_id', $payrollPeriodId);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->get();
    }

    public function upsertComputation(int $employeeId, int $payrollPeriodId, array $data): EmployeeNightDiffComputation
    {
        return $this->computation->updateOrCreate(
            [
                'employee_id' => $employeeId,
                'payroll_period_id' => $payrollPeriodId,
            ],
            $data
        );
    }

    public function sumByPeriod(int $payrollPeriodId): float
    {
        return (float) $this->computation
            ->where('payroll_period_id', $payrollPeriodId)
            ->sum('total_night_amount');
    }

    public function deleteNightDuties(int $employeeId, int $payrollPeriodId): void
    {
        $this->nightDuties
            ->where('employee_id', $employeeId)
            ->where('payroll_period_id', $payrollPeriodId)
            ->delete();
    }

    public function insertNightDuties(array $rows): void
    {
        if (!empty($rows)) {
            $this->nightDuties->insert($rows);
        }
    }
}
