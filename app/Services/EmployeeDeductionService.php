<?php

namespace App\Services;

use App\Contract\EmployeeDeductionInterface;
use App\Data\EmployeeDeductionData;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeTimeRecord;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeeDeductionService
{
    public function __construct(
        private EmployeeDeductionInterface $interface,
        private GuardService $guard
    ) {
        //nothing
    }

    public function getAll(): Collection
    {
        return $this->interface->getAll();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->interface->paginate($perPage, $page);
    }

    public function create(EmployeeDeductionData $dto): EmployeeDeduction
    {
        $this->guard->ensureNotLocked();
        $data = $this->applyBusinessRules($dto);
        return $this->interface->create($data);
    }

    public function upsert(array $dto)
    {
        $this->guard->ensureNotLocked();
        $data = array_map(fn(EmployeeDeductionData $dto) => $this->applyBusinessRules($dto), $dto);
        return $this->interface->upsert($data);
    }

    public function update(int $id, array $data): EmployeeDeduction
    {
        $this->guard->ensureNotLocked();

        $amount = $data['amount'];
        $percentage = $data['percentage'];

        if ($percentage > 0) {
            $base_salary = EmployeeTimeRecord::where('payroll_period_id', $data['payroll_period_id'])
                ->where('employee_id', $data['employee_id'])
                ->first()
                ->base_salary ?? 0;

            $percentageValue = $percentage / 100;
            $amount = round($base_salary * $percentageValue, 2);
        }

        return $this->interface->update($id, [
            'billing_cycle' => $data['billing_cycle'],
            'amount' => $amount,
            'percentage' => $percentage,
            'with_terms' => $data['with_terms'],
            'total_term' => $data['total_term'],
            'is_default' => $data['is_default'],
            'reason' => $data['reason'],
        ]);
    }

    public function delete(int $id): bool
    {
        $this->guard->ensureNotLocked();
        return $this->interface->delete($id);
    }

    public function complete(int $id): EmployeeDeduction
    {
        $this->guard->ensureNotLocked();
        return $this->interface->complete($id);
    }

    public function stop(int $id): EmployeeDeduction
    {
        $this->guard->ensureNotLocked();
        return $this->interface->stop($id);
    }

    public function find(int $id): EmployeeDeduction
    {
        return $this->interface->find($id);
    }

    private function applyBusinessRules(EmployeeDeductionData $data): array
    {
        $amount = $data->amount;

        if ($data->percentage && $data->percentage > 0) {
            $baseSalary = EmployeeTimeRecord::where('payroll_period_id', $data->payroll_period_id)
                ->where('employee_id', $data->employee_id)
                ->value('base_salary') ?? 0;

            $percentageValue = $data->percentage / 100;
            $amount = round($baseSalary * $percentageValue, 2);
        }

        return array_merge($data->toArray(), [
            'amount' => $amount,
        ]);
    }
}