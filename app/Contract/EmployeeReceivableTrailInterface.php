<?php

namespace App\Contract;

use App\Models\EmployeeReceivableTrail;
use Illuminate\Support\Collection;

interface EmployeeReceivableTrailInterface
{
    public function create(array $data): EmployeeReceivableTrail;
    public function show(int $employee_id, int $receivable_id): Collection;
}