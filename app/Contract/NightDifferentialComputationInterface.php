<?php

namespace App\Contract;

use App\Models\EmployeeNightDiffComputation;
use Illuminate\Support\Collection;

interface NightDifferentialComputationInterface
{
    public function getByPeriod(int $payrollPeriodId, ?int $employeeId = null): Collection;
    public function upsertComputation(int $employeeId, int $payrollPeriodId, array $data): EmployeeNightDiffComputation;
    public function sumByPeriod(int $payrollPeriodId): float;
    public function deleteNightDuties(int $employeeId, int $payrollPeriodId): void;
    public function insertNightDuties(array $rows): void;
}
