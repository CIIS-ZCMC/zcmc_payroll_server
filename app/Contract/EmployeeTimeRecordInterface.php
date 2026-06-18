<?php

namespace App\Contract;

use App\Models\EmployeeTimeRecord;
use Illuminate\Support\Collection;

interface EmployeeTimeRecordInterface
{
    public function index(int $payroll_period_id, string $status): Collection;
    public function create(array $data): EmployeeTimeRecord;
    public function update(int $id, array $data): EmployeeTimeRecord;
    public function updateOrCreate(array $data): EmployeeTimeRecord;
    public function deactivate(int $payroll_period_id, int $month, int $year): bool;
    public function include(int $id): bool;
    public function exclude(int $id): bool;
    public function upsert(array $data): int;
}