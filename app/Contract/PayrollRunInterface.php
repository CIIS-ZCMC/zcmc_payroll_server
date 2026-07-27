<?php

namespace App\Contract;

use App\Models\PayrollRun;
use Illuminate\Support\Collection;

interface PayrollRunInterface
{
    public function getAllByPeriod(int $payrollPeriodId): Collection;

    public function find(int $id): PayrollRun;

    public function create(array $data): PayrollRun;

    public function update(int $id, array $data): PayrollRun;

    public function findLatestByPeriod(int $payrollPeriodId): ?PayrollRun;

    public function nextVersion(int $payrollPeriodId): int;

    public function lock(int $id): PayrollRun;

    public function reverse(int $id, string $reason): PayrollRun;
}
