<?php

namespace App\Services;

use App\Contract\EmployeeDeductionInterface;
use App\Contract\EmployeeDeductionTermInterface;
use App\Data\EmployeeDeductionData;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeSalary;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EmployeeDeductionService
{
    public function __construct(
        private EmployeeDeductionInterface $deductions,
        private EmployeeDeductionTermInterface $terms,
    ) {}

    public function getAll(): Collection
    {
        return $this->deductions->getAll();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->deductions->paginate($perPage, $page);
    }

    public function create(EmployeeDeductionData $data, ?array $termData = null): EmployeeDeduction
    {
        return DB::transaction(function () use ($data, $termData) {
            $dto = $this->applyBusinessRules($data);
            $deduction = $this->deductions->create($dto);

            if ($termData !== null) {
                $this->terms->updateOrCreate([
                    ...$termData,
                    'employee_deduction_id' => $deduction->id,
                ]);
            }

            return $deduction;
        });
    }

    public function upsert(array $data)
    {
        return DB::transaction(function () use ($data) {
            $dto = array_map(fn(EmployeeDeductionData $data) => $this->applyBusinessRules($data), $data);
            return $this->deductions->upsert($dto);
        });
    }

    public function update(int $id, EmployeeDeductionData $data): EmployeeDeduction
    {
        return DB::transaction(function () use ($id, $data) {
            $dto = $this->applyBusinessRules($data);
            $deduction = $this->deductions->update($id, $dto);
            return $deduction;
        });
    }

    public function delete(int $id): bool
    {
        return $this->deductions->delete($id);
    }

    public function complete(int $id): EmployeeDeduction
    {
        return DB::transaction(function () use ($id) {
            $deduction = $this->deductions->complete($id);
            return $deduction;
        });
    }

    public function stop(int $id): EmployeeDeduction
    {
        return DB::transaction(function () use ($id) {
            $deduction = $this->deductions->stop($id);
            return $deduction;
        });
    }

    public function find(int $id): EmployeeDeduction
    {
        return $this->deductions->find($id);
    }

    public function listActive(bool $included, int $payrollPeriodId): Collection
    {
        return $this->deductions->listActive($included, $payrollPeriodId);
    }

    private function applyBusinessRules(EmployeeDeductionData $data): array
    {
        $amount = $data->amount;

        if ($data->percentage && $data->percentage > 0) {
            $baseSalary = EmployeeSalary::where('payroll_period_id', $data->payroll_period_id)
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
