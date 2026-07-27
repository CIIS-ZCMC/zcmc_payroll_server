<?php

namespace App\Services;

use App\Contract\EmployeeReceivableInterface;
use App\Contract\EmployeeReceivableTermInterface;
use App\Data\EmployeeReceivableData;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeSalary;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EmployeeReceivableService
{
    public function __construct(
        private EmployeeReceivableInterface $receivables,
        private EmployeeReceivableTermInterface $terms,
    ) {}

    public function getAll(): Collection
    {
        return $this->receivables->getAll();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->receivables->paginate($perPage, $page);
    }

    public function create(EmployeeReceivableData $data, ?array $termData = null): EmployeeReceivable
    {
        return DB::transaction(function () use ($data, $termData) {
            $dto = $this->applyBusinessRules($data);
            $receivable = $this->receivables->create($dto);

            if ($termData !== null) {
                $this->terms->updateOrCreate([
                    ...$termData,
                    'employee_receivable_id' => $receivable->id,
                ]);
            }

            return $receivable;
        });
    }

    public function upsert(array $data)
    {
        return DB::transaction(function () use ($data) {
            $dto = array_map(fn(EmployeeReceivableData $data) => $this->applyBusinessRules($data), $data);
            return $this->receivables->upsert($dto);
        });
    }

    public function update(int $id, EmployeeReceivableData $data): EmployeeReceivable
    {
        return DB::transaction(function () use ($id, $data) {
            $dto = $this->applyBusinessRules($data);
            $receivable = $this->receivables->update($id, $dto);
            return $receivable;
        });
    }

    public function delete(int $id): bool
    {
        return $this->receivables->delete($id);
    }


    public function stop(int $id, ?string $remarks = null, ?int $actorId = null): EmployeeReceivable
    {
        return DB::transaction(function () use ($id, $remarks, $actorId) {
            $receivable = $this->receivables->stop($id);
            return $receivable;
        });
    }

    public function find(int $id): EmployeeReceivable
    {
        return $this->receivables->find($id);
    }


    private function applyBusinessRules(EmployeeReceivableData $data): array
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
