<?php

namespace App\Contract;

use App\Models\EmployeeDeductionTrail;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeDeductionTrailInterface
{
    public function getAllPerPeriod(int $payrollPeriodId, int $page, int $perPage): LengthAwarePaginator;
    public function getAllPagination(int $page, int $perPage): LengthAwarePaginator;
    public function create(array $data): EmployeeDeductionTrail;
    public function find(int $id): ?EmployeeDeductionTrail;
}