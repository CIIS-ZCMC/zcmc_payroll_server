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
        $this->guard->ensureNotLocked((int) $dto->payroll_period_id);
        $data = $this->applyBusinessRules($dto);
        return $this->interface->create($data);
    }

    public function upsert(array $dto): int
    {
        foreach ($dto as $item) {
            $this->guard->ensureNotLocked((int) $item->payroll_period_id);
        }

        $data = array_map(fn(EmployeeReceivableData $dto) => $this->applyBusinessRules($dto), $dto);
        return $this->interface->upsert($data);
    }

    public function update(int $id, array $data): EmployeeReceivable
    {
        $this->guard->ensureNotLocked((int) $data['payroll_period_id']);

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
        $this->guardRecord((int) $id);
        return $this->interface->delete($id);
    }

    public function complete($id): EmployeeReceivable
    {
        $this->guardRecord((int) $id);
        return $this->interface->complete($id);
    }

    public function stop($id): EmployeeReceivable
    {
        $this->guardRecord((int) $id);
        return $this->interface->stop($id);
    }

    /**
     * These three address the row by its own id, so the period to check has to
     * come off the row itself rather than out of the request.
     */
    private function guardRecord(int $id): void
    {
        $periodId = EmployeeReceivable::where('id', $id)->value('payroll_period_id');

        $this->guard->ensurePeriodNotLocked($periodId === null ? null : (int) $periodId);
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
