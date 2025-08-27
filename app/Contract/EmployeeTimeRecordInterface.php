<?php

namespace App\Contract;

use App\Models\EmployeeTimeRecord;

interface EmployeeTimeRecordInterface
{
    public function create(array $data): EmployeeTimeRecord;
    public function update(array $data): bool;
}