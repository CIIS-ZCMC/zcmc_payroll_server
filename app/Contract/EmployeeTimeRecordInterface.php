<?php

namespace App\Contract;

use App\Models\EmployeeTimeRecord;
use Illuminate\Support\Collection;

interface EmployeeTimeRecordInterface
{
    public function index(int $payroll_period_id, string $status): Collection;
    public function create(array $data): EmployeeTimeRecord;
    public function update(int $id, array $data): EmployeeTimeRecord;
}