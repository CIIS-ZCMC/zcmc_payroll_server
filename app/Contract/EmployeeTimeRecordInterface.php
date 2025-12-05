<?php

namespace App\Contract;

use App\Models\EmployeeTimeRecord;
use Illuminate\Support\Collection;

interface EmployeeTimeRecordInterface
{
    public function index(int $payroll_period_id, string $status): Collection;
    public function create(array $data): EmployeeTimeRecord;
    public function update(int $id, array $data): EmployeeTimeRecord;
    public function createOrUpdate(array $data): EmployeeTimeRecord;
    public function deactivate(int $payroll_period_id, int $month, int $year): bool;
}