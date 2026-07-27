<?php

namespace App\Contract;

use App\Models\EmployeeDeductionPayment;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface EmployeeDeductionPaymentInterface
{
    public function getAll(int $employeeDeductionId): Collection;

    public function paginate(
        int $page,
        int $perPage,
        int $employeeDeductionId,
        ?string $fromDate = null,
        ?string $toDate = null
    ): LengthAwarePaginator;

    public function create(array $data): EmployeeDeductionPayment;

    public function existsForRun(int $employeeDeductionId, int $payrollRunId): bool;

    public function getByRun(int $payrollRunId): Collection;

    public function paginateByRun(int $payrollRunId, int $perPage, int $page): LengthAwarePaginator;
}
