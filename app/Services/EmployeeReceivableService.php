<?php

namespace App\Services;

use App\Contract\EmployeeReceivableInterface;
use App\Data\EmployeeReceivableData;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeTimeRecord;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeReceivableService
{
    public function __construct(
        private EmployeeReceivableInterface $interface,
        private GuardService $guard
    ) {
        //Nothing
    }

    public function getAll(): Collection
    {
        return $this->interface->getAll();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->interface->paginate($perPage, $page);
    }

    public function create(EmployeeReceivableData $dto): EmployeeReceivable
    {
        $this->guard->ensureNotLocked();
        $data = $this->applyBusinessRules($dto);
        return $this->interface->create($data);
    }

    public function upsert(array $dto): int
    {
        $this->guard->ensureNotLocked();
        $data = array_map(fn(EmployeeReceivableData $dto) => $this->applyBusinessRules($dto), $dto);
        return $this->interface->upsert($data);
    }

    public function update(int $id, array $data): EmployeeReceivable
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
            'is_default' => $data['is_default'],
            'reason' => $data['reason'],
        ]);
    }

    public function delete($id): bool
    {
        $this->guard->ensureNotLocked();
        return $this->interface->delete($id);
    }

    public function complete($id): EmployeeReceivable
    {
        $this->guard->ensureNotLocked();
        return $this->interface->complete($id);
    }

    public function stop($id): EmployeeReceivable
    {
        $this->guard->ensureNotLocked();
        return $this->interface->stop($id);
    }

    public function find(int $id): EmployeeReceivable
    {
        return $this->interface->find($id);
    }

    private function applyBusinessRules(EmployeeReceivableData $data): array
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
