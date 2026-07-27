<?php

namespace App\Contract;

use App\Models\EmployeeReceivablePayment;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeReceivablePaymentInterface
{
    public function getAll(int $employeeReceivableId): Collection;

    public function paginate(
        int $page,
        int $perPage,
        int $employeeReceivableId,
        ?string $fromDate = null,
        ?string $toDate = null
    ): LengthAwarePaginator;

    public function create(array $data): EmployeeReceivablePayment;

    public function existsForRun(int $employeeReceivableId, int $payrollRunId): bool;

    public function getByRun(int $payrollRunId): Collection;

    public function paginateByRun(int $payrollRunId, int $perPage, int $page): LengthAwarePaginator;
}
