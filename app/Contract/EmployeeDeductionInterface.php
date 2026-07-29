<?php

namespace App\Contract;

use App\Models\EmployeeDeduction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface EmployeeDeductionInterface
{
    public function getAll(): Collection;

    public function paginate(int $perPage, int $page): LengthAwarePaginator;

    public function create(array $data): EmployeeDeduction; // single update or storing

    public function upsert(array $data): int; // bulk update or storing

    /**
     * Upsert a standing deduction for an employee, keyed by
     * (employee_id, deduction_id, payroll_period_id) so re-imports update in
     * place instead of duplicating.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function updateOrCreateStanding(int $employeeId, int $deductionId, array $attributes): EmployeeDeduction;

    public function update(int $id, array $data): EmployeeDeduction;

    public function delete(int $id): bool;

    public function complete(int $id): EmployeeDeduction;

    public function stop(int $id): EmployeeDeduction;

    public function find(int $id): EmployeeDeduction;

    public function findByPayrollPeriod(int $payrollPeriodId): Collection;

    public function listActive(?bool $included = null, ?int $payrollPeriodId = null): Collection;
}
