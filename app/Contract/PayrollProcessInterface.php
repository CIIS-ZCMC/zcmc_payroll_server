<?php

namespace App\Contract;

use App\Models\PayrollProcess;

interface PayrollProcessInterface
{
    public function find(int $payrollPeriodId, int $payrollType): ?PayrollProcess;
    public function create(array $data): PayrollProcess;
    public function update(int $id, array $data): PayrollProcess;
    public function updateProcess(int $id, int $currentStep, string $status): PayrollProcess;
}
