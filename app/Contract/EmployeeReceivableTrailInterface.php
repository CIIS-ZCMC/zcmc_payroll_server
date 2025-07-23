<?php

namespace App\Contract;

use App\Models\EmployeeReceivableTrail;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeReceivableTrailInterface
{
    public function getAllPerPeriod(int $payrollPeriodId, int $page, int $perPage): LengthAwarePaginator;
    public function getAllPagination(int $page, int $perPage): LengthAwarePaginator;
    public function find(int $id): ?EmployeeReceivableTrail;
}