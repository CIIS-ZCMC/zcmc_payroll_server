<?php

namespace App\Contract\Repositories;

use App\Contract\PayrollPeriodInterface;
use App\Models\PayrollPeriod;
use Illuminate\Support\Collection;

class PayrollPeriodRepository implements PayrollPeriodInterface
{
    public function __construct(private PayrollPeriod $model)
    {
        //nothing
    }

    public function getall(): Collection
    {
        return $this->model->get();
    }

    public function getActive(): ?PayrollPeriod
    {
        return $this->model->where('is_active', true)->first();
    }

    public function getCurrent(): ?PayrollPeriod
    {
        $now = now();
        $firstHalfThreshold = 15;

        $currentPeriodType = $now->day <= $firstHalfThreshold ? 'first_half' : 'second_half';

        return $this->model->where('year', $now->year)
            ->where('month', $now->month)
            ->where('period_type', $currentPeriodType)
            ->where('employment_type', 'regular')
            ->first();
    }

    public function setActive(int $id): PayrollPeriod
    {
        $model = $this->model->findOrFail($id);
        $model->is_active = true;
        $model->save();
        return $model->fresh();
    }

    public function deactivateOthers(int $id): bool
    {
        return $this->model->where('id', '!=', $id)->update(['is_active' => false]);
    }

    public function create(array $data): PayrollPeriod
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): PayrollPeriod
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model->fresh();
    }

    public function lock(int $id): PayrollPeriod
    {
        $model = $this->model->find($id);
        $model->update(['locked_at' => now()]);
        return $model->fresh();
    }

    public function createOrUpdate(array $data): PayrollPeriod
    {
        return $this->model->updateOrCreate(
            [
                'year' => $data['year'],
                'month' => $data['month'],
                'employment_type' => $data['employment_type'],
                'period_type' => $data['period_type'],
            ],
            $data
        );
    }

    public function findPeriod(int $year, int $month, string $periodType, string $employmentType): ?PayrollPeriod
    {
        return $this->model->where('year', $year)
            ->where('month', $month)
            ->where('period_type', $periodType)
            ->where('employment_type', $employmentType)
            ->first();
    }

    public function isLocked(int $id): bool
    {
        return $this->model->find($id)->locked_at !== null;
    }

}