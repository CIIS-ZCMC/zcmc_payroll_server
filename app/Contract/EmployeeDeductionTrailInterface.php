<?php

namespace App\Contract;

use App\Models\EmployeeDeductionTrail;
use Illuminate\Support\Collection;

interface EmployeeDeductionTrailInterface
{
    public function create(array $data): EmployeeDeductionTrail;
    public function show(int $employee_id, int $deduction_id): Collection;
}